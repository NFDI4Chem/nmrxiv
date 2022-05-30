<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Actions\Project\CreateNewProject;
use App\Actions\Project\UpdateProject;
use App\Actions\Project\InviteProjectMember;
use App\Actions\Project\AddProjectMember;
use App\Actions\Project\UpdateProjectMemberRole;
use App\Actions\Project\RemoveProjectMember;
use App\Models\Project;
use App\Models\User;
use App\Models\Study;
use App\Models\ProjectInvitation;
use Laravel\Jetstream\Jetstream;
use Auth;
use Illuminate\Contracts\Auth\StatefulGuard;
use Laravel\Fortify\Actions\ConfirmPassword;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;


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

    public function publicProject(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        if($project->is_public){
            return  $project;
        }else{
            return back(403);
        }
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

    public function toggleStarred(Request $request, Project $project){
        $user = $request->user();
        $hasPassword = false;
        if($user->password){
            $hasPassword = true;
        }        
        return response()->json([
            'hasPassword' => $hasPassword
        ]);
    }

    public function destroy(Request $request, StatefulGuard $guard, Project $project)
    {   
        $confirmed = app(ConfirmPassword::class)(
            $guard, $request->user(), $request->password
        );

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $project->delete();

        return redirect()->route('dashboard')->with('success', 'Project deleted successfully');
    }

    public function activity(Request $request, Project $project)
    {
        return response()->json(['audit' => $project->audits()->with('user')->orderBy('created_at', 'desc')->get()]);
    }

    /**
     * Add a new team member to a project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $projectId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function memberStore(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        app(InviteProjectMember::class)->invite(
            $request->user(),
            $project,
            $request->email ?: '',
            $request->role,
            $request->message
        );
    
        return back(303);
    }

    /**
     * Accept a project invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\ProjectInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInvitation(Request $request, ProjectInvitation $invitation)
    {
        app(AddProjectMember::class)->add(
            $invitation->project->owner,
            $invitation->project,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        return redirect(config('fortify.home'))->banner(
            __('Great! You have accepted the invitation to join the :project project.', ['project' => $invitation->project->name]),
        );
    }

    /**
     * Cancel the given project invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\ProjectInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyInvitation(Request $request, ProjectInvitation $invitation)
    {
        if (! Gate::forUser($request->user())->check('removeProjectMember', $invitation->project)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }

    /**
     * Update the given project member's role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $projectId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMemberRole(Request $request, $projectId, $userId)
    {
        app(UpdateProjectMemberRole::class)->update(
            $request->user(),
            Project::findOrFail($projectId),
            $userId,
            $request->role
        );

        return back(303);
    }


    /**
     * Remove the given user from the given project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $projectId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeMember(Request $request, $projectId, $userId)
    {
        $project = Project::findOrFail($projectId);

        app(RemoveProjectMember::class)->remove(
            $request->user(),
            $project,
            $user = User::find($userId)
        );

        if ($request->user()->id === $user->id) {
            return redirect(config('fortify.home'));
        }

        return back(303);
    }
}
