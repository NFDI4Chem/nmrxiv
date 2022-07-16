<?php

namespace App\Http\Controllers;

use App\Actions\Study\AddStudyMember;
use App\Models\StudyInvitation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudyInvitationController extends Controller
{
    /**
     * Accept a study invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\StudyInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInvitation(Request $request, StudyInvitation $invitation)
    {
        app(AddStudyMember::class)->add(
            $invitation->study->owner,
            $invitation->study,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        return redirect(config('fortify.home'))->banner(
            __('Great! You have accepted the invitation to join the :study study.', ['study' => $invitation->study->name]),
        );
    }

    /**
     * Cancel the given study invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\StudyInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyInvitation(Request $request, StudyInvitation $invitation)
    {
        if (! Gate::forUser($request->user())->check('removeStudyMember', $invitation->study)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
