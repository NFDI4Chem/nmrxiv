<?php

namespace App\Http\Controllers;

use App\Actions\Study\InviteStudyMember;
use App\Actions\Study\RemoveStudyMember;
use App\Actions\Study\UpdateStudyMemberRole;
use App\Models\Study;
use App\Models\User;
use Illuminate\Http\Request;

class StudyMemberController extends Controller
{
    /**
     * Add a new team member to a study.
     *
     * @param  int  $studyId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function memberStore(Request $request, $studyId)
    {
        $study = Study::findOrFail($studyId);

        app(InviteStudyMember::class)->invite(
            $request->user(),
            $study,
            $request->email ?: '',
            $request->role,
            $request->message
        );

        return back(303);
    }

    /**
     * Update the given study member's role.
     *
     * @param  int  $studyId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMemberRole(Request $request, $studyId, $userId)
    {
        app(UpdateStudyMemberRole::class)->update(
            $request->user(),
            Study::findOrFail($studyId),
            $userId,
            $request->role
        );

        return back(303);
    }

    /**
     * Remove the given user from the given study.
     *
     * @param  int  $studyId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeMember(Request $request, $studyId, $userId)
    {
        $study = Study::findOrFail($studyId);

        app(RemoveStudyMember::class)->remove(
            $request->user(),
            $study,
            $user = User::find($userId)
        );

        if ($request->user()->id === $user->id) {
            return redirect(config('fortify.home'));
        }

        return back(303);
    }
}
