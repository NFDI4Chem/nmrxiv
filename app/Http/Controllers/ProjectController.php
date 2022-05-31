<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Actions\Project\CreateNewProject;
use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Actions\ConfirmPassword;
use Illuminate\Validation\ValidationException;
use App\Actions\Project\UpdateProject;
use App\Actions\Project\DeleteProject;
use Laravel\Jetstream\Jetstream;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Study;
use Inertia\Inertia;
use Auth;

class ProjectController extends Controller
{

    public function publicProjectView(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        if($project->is_public){
            return Inertia::render('Public/Project', [
                'project' => $project,
                'studies' => $project->studies
            ]);
        }
    }

    public function publicProjectsView(Request $request)
    {
        $projects = Project::where('is_public', TRUE)->simplePaginate(15);

        return Inertia::render('Public/Projects', [
            'projects' => $projects
        ]);
        
    }

    public function show(Request $request, Project $project)
    {
        $team = $project->nonPersonalTeam;
        return Inertia::render('Project/Show', [
            'project' => $project->load('projectInvitations'),
            'team' => $team ? $team->load('users', 'owner') : null,
            'members' => $project->users,
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
        return Inertia::render('Project/Settings', [
            'project' => $project,
            'studies' => $project->studies
        ]);
    }

    public function activity(Request $request, Project $project)
    {
        return response()->json(['audit' => $project->audits()->with('user')->orderBy('created_at', 'desc')->get()]);
    }

    public function store(Request $request, CreateNewProject $creator)
    {
        $project = $creator->create($request->all());
        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Project created successfully');
    }

    public function update(Request $request, UpdateProject $updater, Project $project)
    {
        $updater->update($project, $request->all());
        return $request->wantsJson() ? new JsonResponse('', 200) : back()->with('success', 'Project updated successfully');
    }

    public function destroy(Request $request, StatefulGuard $guard, Project $project, DeleteProject $creator)
    {   
        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (!$confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $creator->delete($project);

        return redirect()->route('dashboard')->with('success', 'Project deleted successfully');
    }
}
