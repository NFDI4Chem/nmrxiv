<?php

namespace App\Actions\Study;

use App\Events\StudyMemberUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Rules\Role;

class UpdateStudyMemberRole
{
    /**
     * Update the role for the given study member.
     *
     * @param  mixed  $user
     * @param  mixed  $study
     * @param  int  $studyMemberId
     * @return void
     */
    public function update($user, $study, $studyMemberId, string $role)
    {
        Gate::forUser($user)->authorize('updateStudyMember', $study);

        Validator::make([
            'role' => $role,
        ], [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $study->users()->updateExistingPivot($studyMemberId, [
            'role' => $role,
        ]);

        StudyMemberUpdated::dispatch($study->fresh(), User::find($studyMemberId));
    }
}
