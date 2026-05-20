<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardIndexRequest;
use App\Models\Project;
use App\Models\Study;
use App\Models\Team;
use App\Models\User;
use App\Support\Dashboard\CompoundLibraryRankedStudiesQuery;
use App\Support\Dashboard\WorkspaceMoleculeAggregates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard(DashboardIndexRequest $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;
        $filters = $request->dashboardFilters();

        $emptyProjects = Project::query()->whereRaw('0 = 1')->paginate(
            $filters['projects_per_page'],
            ['*'],
            'projects_page',
            $filters['projects_page']
        )->withQueryString();

        $emptySamples = Study::query()->whereRaw('0 = 1')->paginate(
            $filters['samples_per_page'],
            ['*'],
            'samples_page',
            $filters['samples_page']
        )->withQueryString();

        if (! $team) {
            return Inertia::render('Dashboard', [
                'filters' => $filters,
                'team' => null,
                'projects' => $emptyProjects,
                'samples' => $emptySamples,
                'workspaceProjects' => [],
                'workspaceStudies' => [],
                'hasProjects' => false,
                'hasSamples' => false,
                'teamRole' => $user->teamRole($team),
                'user' => $user,
            ]);
        }

        $team->users = $team->allUsers();

        $workspace = $filters['workspace'] ?? 'default';

        if ($workspace !== 'default') {
            [$workspaceProjects, $workspaceStudies] = $this->workspaceProjectAndStudyLists($user, $workspace);

            $hasProjects = $this->scopedProjectsQuery($user, $team)->exists();
            $hasSamples = $this->scopedSamplesQuery($user, $team)->exists();

            return Inertia::render('Dashboard', [
                'filters' => $filters,
                'team' => $team,
                'projects' => $emptyProjects,
                'samples' => $emptySamples,
                'workspaceProjects' => $workspaceProjects,
                'workspaceStudies' => $workspaceStudies,
                'hasProjects' => $hasProjects,
                'hasSamples' => $hasSamples,
                'teamRole' => $user->teamRole($team),
                'user' => $user,
            ]);
        }

        $projectsQuery = $this->scopedProjectsQuery($user, $team)
            ->with(['users', 'owner', 'tags', 'draft'])
            ->orderByDesc('updated_at');

        $this->applyStatusFilter($projectsQuery, $filters['projects_status']);
        $this->applySearchToProjects($projectsQuery, $filters['projects_q']);

        $projects = $projectsQuery->paginate(
            $filters['projects_per_page'],
            ['*'],
            'projects_page',
            $filters['projects_page']
        )->withQueryString();

        $samplesQuery = $this->scopedSamplesQuery($user, $team)
            ->with([
                'users',
                'owner',
                'sample.molecules' => function (BelongsToMany $query) use ($user, $team): void {
                    WorkspaceMoleculeAggregates::applyToMoleculeRelation($query, $user, $team);
                },
            ])
            ->orderByDesc('updated_at');

        $this->applyStudyVisibilityFilter($samplesQuery, $filters['samples_status']);
        $this->applySearchToStudies($samplesQuery, $filters['samples_q']);

        $samples = $samplesQuery->paginate(
            $filters['samples_per_page'],
            ['*'],
            'samples_page',
            $filters['samples_page']
        )->withQueryString();

        $hasProjects = $this->scopedProjectsQuery($user, $team)->exists();
        $hasSamples = $this->scopedSamplesQuery($user, $team)->exists();

        return Inertia::render('Dashboard', [
            'filters' => $filters,
            'team' => $team,
            'projects' => $projects,
            'samples' => $samples,
            'workspaceProjects' => [],
            'workspaceStudies' => [],
            'hasProjects' => $hasProjects,
            'hasSamples' => $hasSamples,
            'teamRole' => $user->teamRole($team),
            'user' => $user,
        ]);
    }

    /**
     * @return array{0: array<int, mixed>, 1: array<int, mixed>}
     */
    protected function workspaceProjectAndStudyLists(User $user, string $workspace): array
    {
        return match ($workspace) {
            'shared' => [
                $user->sharedProjects()->with(['owner', 'tags', 'draft'])->get()->all(),
                $user->sharedStudies()->with(['owner', 'sample.molecules'])->get()->all(),
            ],
            'recent' => [
                $this->recentProjectsForUser($user),
                [],
            ],
            'starred' => [
                Project::query()
                    ->where('is_deleted', false)
                    ->whereHasBookmark($user)
                    ->with(['owner', 'tags', 'draft'])
                    ->get()
                    ->all(),
                Study::query()
                    ->where('is_deleted', false)
                    ->whereHasBookmark($user)
                    ->with(['owner', 'sample.molecules'])
                    ->get()
                    ->all(),
            ],
            'trashed' => [
                Project::query()
                    ->where('owner_id', $user->id)
                    ->where('is_deleted', true)
                    ->with(['owner', 'tags', 'draft'])
                    ->get()
                    ->all(),
                [],
            ],
            default => [[], []],
        };
    }

    /**
     * @return array<int, mixed>
     */
    protected function recentProjectsForUser(User $user): array
    {
        $projects = $user->activeProjects()->with(['owner', 'tags', 'draft'])->get();

        foreach ($user->allTeams() as $teamModel) {
            $projects = $projects->concat(
                $teamModel->activeProjects()->with(['owner', 'tags', 'draft'])->get()
            );
        }

        return $projects->unique('id')->sortByDesc(fn ($project) => $project->updated_at)->values()->all();
    }

    /**
     * @return Builder<Project>
     */
    protected function scopedProjectsQuery(User $user, Team $team): Builder
    {
        $query = Project::query()->where('is_deleted', false);

        if ($team->personal_team) {
            $query->where('team_id', $team->id)->where('owner_id', $user->id);
        } else {
            $query->where('team_id', $team->id);
        }

        return $query;
    }

    /**
     * Studies in the current team (standalone, or under a project that is not in the trash).
     * Duplicate compounds (same InChI key / primary molecule) collapse to the study updated most recently.
     *
     * @return Builder<Study>
     */
    protected function scopedSamplesQuery(User $user, Team $team): Builder
    {
        return Study::query()->whereIn(
            'studies.id',
            function (QueryBuilder $query) use ($user, $team): void {
                $query->select('ranked.id')
                    ->fromSub(CompoundLibraryRankedStudiesQuery::build($user, $team), 'ranked')
                    ->where('ranked.rn', '=', 1);
            }
        );
    }

    protected function applyStatusFilter(Builder $query, string $status): void
    {
        if ($status !== 'all') {
            $query->where('status', $status);
        }
    }

    /**
     * Compound library tab: filter studies by public vs private visibility.
     */
    protected function applyStudyVisibilityFilter(Builder $query, string $visibility): void
    {
        if ($visibility === 'public') {
            $query->where('is_public', true);
        } elseif ($visibility === 'private') {
            $query->where(function (Builder $q): void {
                $q->where('is_public', false)
                    ->orWhereNull('is_public');
            });
        }
    }

    protected function applySearchToProjects(Builder $query, string $term): void
    {
        $term = trim($term);
        if ($term === '') {
            return;
        }

        $table = $query->getModel()->getTable();
        $like = '%'.addcslashes($term, '%_\\').'%';

        $query->where(function (Builder $q) use ($like, $table) {
            $q->where($table.'.name', 'like', $like)
                ->orWhere($table.'.description', 'like', $like)
                ->orWhereRaw('CAST('.$table.'.id AS VARCHAR) LIKE ?', [$like])
                ->orWhere($table.'.uuid', 'like', $like);
        });
    }

    protected function applySearchToStudies(Builder $query, string $term): void
    {
        $term = trim($term);
        if ($term === '') {
            return;
        }

        $table = $query->getModel()->getTable();
        $like = '%'.addcslashes($term, '%_\\').'%';

        $query->where(function (Builder $q) use ($like, $table) {
            $q->where($table.'.name', 'like', $like)
                ->orWhere($table.'.description', 'like', $like)
                ->orWhereRaw('CAST('.$table.'.id AS VARCHAR) LIKE ?', [$like])
                ->orWhere($table.'.uuid', 'like', $like);
        });
    }

    public function onboardingStatus(Request $request, $status)
    {
        $user = $request->user();

        if ($user) {
            if ($status == 'complete') {
                $user->onboarded = true;
                $user->save();

                return $user;
            }
        }
    }

    /**
     * Update the database to skip displaying primer
     *
     * @return void
     */
    public function skipPrimer(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->primed = true;
            $user->save();
        }
    }
}
