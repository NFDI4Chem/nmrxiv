<?php

namespace App\Http\Controllers;

use App\Actions\Project\CreateNewProject;
use App\Actions\Project\DeleteProject;
use App\Actions\Project\UpdateProject;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
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
use Maize\Markable\Models\Like;

class ProjectController extends Controller
{
    public function publicProjectView(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        if (! $project->is_public) {
            if (! Gate::forUser($request->user())->check('viewProject', $project)) {
                throw new AuthorizationException;
            }
        }

        return Inertia::render('Public/Project', [
            'project' => (new ProjectResource($project))->lite(false, ['users', 'studies', 'license', 'files']),
        ]);
    }

    public function publicProjectsView(Request $request)
    {
        $projects = Cache::rememberForever('projects', function () {
            return ProjectResource::collection(Project::where('is_public', true)->orderByDesc('updated_at')->paginate(15));
        });

        return Inertia::render('Public/Projects', [
            'projects' => $projects,
        ]);
    }

    public function toggleUpVote(Request $request, Project $project)
    {
        return Like::toggle($project, $request->user());
    }

    public function status(Request $request, Project $project)
    {
        return response()->json(['status' => $project->status, 'logs' => $project->process_logs]);
    }

    public function show(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        $team = $project->nonPersonalTeam;

        return Inertia::render('Project/Show', [
            'project' => $project->load('projectInvitations', 'tags'),
            'team' => $team ? $team->load('users', 'owner') : null,
            'members' => $project->allUsers(),
            'availableRoles' => array_values(Jetstream::$roles),
            'studies' => $project->studies,
            'projectRole' => $project->userProjectRole(Auth::user()->email),
            'projectPermissions' => [
                'canDeleteProject' => Gate::check('deleteProject', $project),
                'canUpdateProject' => Gate::check('updateProject', $project),
            ],
        ]);
    }

    public function settings(Request $request, Project $project)
    {
        if (! Gate::forUser($request->user())->check('viewProject', $project)) {
            throw new AuthorizationException;
        }

        return Inertia::render('Project/Settings', [
            'project' => $project,
            'studies' => $project->studies,
        ]);
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

        // if (! Gate::forUser($request->user())->check('createProject', $project)) {
        //     throw new AuthorizationException;
        // }

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
