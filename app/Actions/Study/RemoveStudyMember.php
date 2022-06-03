<?php

namespace App\Actions\Study;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use App\Events\StudyMemberRemoved;
use App\Models\User;

class RemoveStudyMember
{
    /**
     * Remove the study member from the given study.
     *
     * @param  mixed  $user
     * @param  mixed  $study
     * @param  mixed  $studyMember
     * @return void
     */
    public function remove($user, $study, $studyMember)
    {
        $this->authorize($user, $study, $studyMember);

        $this->ensureUserDoesNotOwnStudy($studyMember, $study);

        $study->removeUser($studyMember);

        StudyMemberRemoved::dispatch($study, $studyMember);

        return redirect()->route('dashboard')->with('success', 'Member removed successfully');
    }

    /**
     * Authorize that the user can remove the study member.
     *
     * @param  mixed  $user
     * @param  mixed  $study
     * @param  mixed  $studyMember
     * @return void
     */
    protected function authorize($user, $study, $studyMember)
    {
        if (! Gate::forUser($user)->check('removeStudyMember', $study) &&
            $user->id !== $studyMember->id) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the study.
     *
     * @param  mixed  $studyMember
     * @param  mixed  $study
     * @return void
     */
    protected function ensureUserDoesNotOwnStudy($studyMember, $study)
    {
        if ($studyMember->id === $study->owner->id) {
            throw ValidationException::withMessages([
                'study' => [__('You may not leave a study that you created.')],
            ])->errorBag('removeStudyMember');
        }
    }
}
