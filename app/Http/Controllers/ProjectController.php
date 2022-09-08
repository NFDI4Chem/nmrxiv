<?php

namespace App\Http\Controllers;

use App\Actions\License\GetLicense;
use App\Actions\Project\ArchiveProject;
use App\Actions\Project\CreateNewProject;
use App\Actions\Project\DeleteProject;
use App\Actions\Project\RestoreProject;
use App\Actions\Project\UpdateProject;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\StudyResource;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Actions\ConfirmPassword;
use Laravel\Jetstream\Jetstream;
use Maize\Markable\Models\Bookmark;
use Maize\Markable\Models\Like;

class ProjectController extends Controller
{
    public function publicProjectView(Request $request, $owner, $slug)
    {
        $user = User::where('username', $owner)->firstOrFail();

        $project = Project::where([['slug', $slug], ['owner_id', $user->id]])->firstOrFail();

        if (! $project->is_public) {
            if (! Gate::forUser($request->user())->check('viewProject', $project)) {
                throw new AuthorizationException;
            }
        }

        $tab = $request->get('tab');

        if ($tab == 'info') {
            return Inertia::render('Public/Project/Show', [
                'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'studies') {
            return Inertia::render('Public/Project/Studies', [
                'project' => (new ProjectResource($project))->lite(false, ['studies']),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'files') {
            return Inertia::render('Public/Project/Files', [
                'project' => (new ProjectResource($project))->lite(false, ['files']),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'license') {
            return Inertia::render('Public/Project/License', [
                'project' => (new ProjectResource($project))->lite(false, ['license']),
                'tab' => $tab,
            ]);
        } elseif ($tab == 'study') {
            $studyId = $request->get('id');
            $study = Study::where([['slug', $studyId], ['owner_id', $user->id],  ['project_id', $project->id]])->firstOrFail();

            return Inertia::render('Public/Project/Study', [
                'project' => (new ProjectResource($project))->lite(false, []),
                'tab' => $tab,
                'study' => $study->load('tags'),
            ]);
        } else {
            return Inertia::render('Public/Project/Show', [
                'project' => (new ProjectResource($project))->lite(false, ['users', 'authors', 'citations']),
                'tab' => 'info',
            ]);
        }
    }

    public function publicProjectsView(Request $request)
    {
        // $projects = Cache::rememberForever('projects', function () {
        $projects = ProjectResource::collection(Project::where('is_public', true)->filter($request->only('search', 'sort', 'mode'))->paginate(12)->withQueryString());
        // });

        return Inertia::render('Public/Projects', [
            'filters' => $request->all('search', 'sort', 'mode'),
            'projects' => $projects,
        ]);
    }

    public function toggleUpVote(Request $request, Project $project)
    {
        return Like::toggle($project, $request->user());
    }

    public function toggleStarred(Request $request, Project $project)
    {
        return Bookmark::toggle($project, $request->user());
    }

    public function status(Request $request, Project $project)
    {
        return response()->json(['status' => $project->status, 'logs' => $project->process_logs]);
    }

    public function show(Request $request, Project $project, GetLicense $getLicense)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        $team = $project->nonPersonalTeam;
        $license = null;
        if ($project->license_id) {
            $license = $getLicense->getLicensebyId($project->license_id);
        }

        return Inertia::render('Project/Show', [
            'project' => $project->load('projectInvitations', 'tags', 'authors', 'citations'),
            'team' => $team ? $team->load(['users', 'owner']) : null,
            'members' => $project->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'role' => $project->userProjectRole(Auth::user()->email),
            'license' => $license ? $license[0] : null,
            'projectPermissions' => [
                'canDeleteProject' => Gate::check('deleteProject', $project),
                'canUpdateProject' => Gate::check('updateProject', $project),
            ],
        ]);
    }

    public function studies(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        return StudyResource::collection(Study::where('project_id', $project->id)->filter($request->only('search', 'sort', 'mode'))->paginate(9)->withQueryString());
    }

    public function settings(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        return Inertia::render('Project/Settings', [
            'project' => $project,
        ]);
    }

    public function restore(Request $request, StatefulGuard $guard, Project $project, RestoreProject $creator)
    {
        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $creator->restore($project);

        return redirect()->route('dashboard')->with('success', 'Project restored successfully');
    }

    public function toggleArchive(Request $request, StatefulGuard $guard, Project $project, ArchiveProject $creator)
    {
        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $creator->toggle($project);

        return redirect()->route('dashboard')->with('success', 'Project archive state updated successfully');
    }

    public function activity(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        return response()->json(['audit' => $project->audits()->with('user')->orderBy('created_at', 'desc')->get()]);
    }

    public function store(Request $request, CreateNewProject $creator)
    {
        if (! Gate::forUser($request->user())->check('createProject', $project)) {
            throw new AuthorizationException;
        }

        $project = $creator->create($request->all());

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Project created successfully');
    }

    public function update(Request $request, UpdateProject $updater, Project $project)
    {
        if (! Gate::forUser($request->user())->check('updateProject', $project)) {
            throw new AuthorizationException;
        }

        $updater->update($project, $request->all());

        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Project updated successfully');
    }

    public function destroy(Request $request, StatefulGuard $guard, Project $project, DeleteProject $creator)
    {
        if (! Gate::forUser($request->user())->check('deleteProject', $project)) {
            throw new AuthorizationException;
        }

        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $creator->delete($project);

        return redirect()->route('dashboard')->with('success', 'Project deleted successfully');
    }
}
