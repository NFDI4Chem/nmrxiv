<?php

namespace App\Actions\Study;

use App\Events\InvitingStudyMember;
use App\Mail\StudyInvitation;
use App\Models\User;
use App\Notifications\StudyInviteNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class InviteStudyMember
{
    /**
     * Invite a new study member to the given study.
     *
     * @param  mixed  $user
     * @param  mixed  $study
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    public function invite($user, $study, string $email, string $role = null, string $message = null)
    {
        Gate::forUser($user)->authorize('addStudyMember', $study);

        $this->validate($study, $email, $role, $message);

        InvitingStudyMember::dispatch($study, $email, $role, $message);

        $invitation = $study->studyInvitations()->create([
            'email' => $email,
            'role' => $role,
            'message' => $message,
            'invited_by' => $user->name,
        ]);

        Mail::to($email)->send(new StudyInvitation($invitation));

        $invitedUser = User::where('email', $invitation->email)->first();

        if ($invitedUser) {
            $invitedUser->notify(new StudyInviteNotification($invitation));
        }
    }

    /**
     * Validate the invite member operation.
     *
     * @param  mixed  $study
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    protected function validate($study, string $email, ?string $role, ?string $message)
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
            'message' => $message,
        ], $this->rules($study), [
            'email.unique' => __('This user has already been invited to the study.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnStudy($study, $email)
        )->validateWithBag('addModelMember');
    }

    /**
     * Get the validation rules for inviting a study member.
     *
     * @param  mixed  $study
     * @return array
     */
    protected function rules($study)
    {
        return array_filter([
            'email' => ['required', 'email', Rule::unique('study_invitations')->where(function ($query) use ($study) {
                $query->where('study_id', $study->id);
            })],
            'role' => Jetstream::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the study.
     *
     * @param  mixed  $study
     * @param  string  $email
     * @return \Closure
     */
    protected function ensureUserIsNotAlreadyOnStudy($study, string $email)
    {
        return function ($validator) use ($study, $email) {
            $validator->errors()->addIf(
                $study->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the study.')
            );
        };
    }
}
