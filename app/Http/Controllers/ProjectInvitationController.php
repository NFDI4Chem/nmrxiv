<?php

namespace App\Http\Controllers;

use App\Models\ProjectInvitation;
use App\Actions\Project\AddProjectMember;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class ProjectInvitationController extends Controller
{
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
}
