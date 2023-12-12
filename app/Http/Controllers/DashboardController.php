<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Study;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;
        $projects = [];

        if ($team) {
            $team->users = $team->allUsers();
            if (! $team->personal_team) {
                $projects = Project::with('users', 'owner')->where([['team_id', $team->id], ['is_deleted', false]])
                    ->when($request->input('sort'), function ($query, $sort) {
                        $query->sort(["{$sort}" => 'desc']);
                    }, function ($query) {
                        $query->sort(['updated_at' => 'desc']);
                    })->get();
            } else {
                $projects = Project::with('users', 'owner')->where([['owner_id', $user->id], ['is_deleted', false], ['team_id', $team->id]])
                    ->when($request->input('sort'), function ($query, $sort) {
                        $query->sort(["{$sort}" => 'desc']);
                    }, function ($query) {
                        $query->sort(['updated_at' => 'desc']);
                    })->get();
            }
        }

        return Inertia::render('Dashboard', [
            'filters' => $request->all('action', 'draft_id'),
            'team' => $team,
            'projects' => $projects->load('tags'),
            'teamRole' => $user->teamRole($team),
        ]);
    }

    public function sharedWithMe(Request $request)
    {
        $user = $request->user();

        $projects = $user->sharedProjects->load('owner');

        $studies = $user->sharedStudies->load('owner');

        return Inertia::render('SharedWithMe', [
            'projects' => $projects,
            'studies' => $studies,
        ]);
    }

    public function trashed(Request $request)
    {
        $user = $request->user();
        $projects = Project::where('owner_id', $user->id)
            ->Where('is_deleted', true)
            ->get();

        return Inertia::render('Trashed', [
            'projects' => $projects,
        ]);
    }

    public function starred(Request $request)
    {
        $user = $request->user();

        $projects = Project::where([['is_deleted', false]])->whereHasBookmark(
            auth()->user()
        )->get();

        $studies = Study::where([['is_deleted', false]])->whereHasBookmark(
            auth()->user()
        )->get();

        return Inertia::render('Starred', [
            'projects' => $projects->load('owner'),
            'studies' => $studies,
        ]);
    }

    public function recent(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        $projects = $user->activeProjects->load('owner');

        foreach ($user->allTeams() as $team) {
            $projects = $projects->concat($team->activeProjects->load('owner'));
        }

        $projects = $projects->unique()->sortByDesc(function ($project) {
            return $project['updated_at'];
        })->values();

        return Inertia::render('Recent', [
            'team' => $team,
            'projects' => $projects,
        ]);
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
