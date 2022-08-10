<?php

namespace App\Http\Controllers;

use App\Actions\Project\AddProjectMember;
use App\Models\ProjectInvitation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
