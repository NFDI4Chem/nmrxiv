<?php

namespace App\Actions\Study;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Events\AddingStudyMember;
use App\Events\StudyMemberAdded;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class AddStudyMember
{
    /**
     * Add a new study member to the given study.
     *
     * @param  mixed  $user
     * @param  mixed  $study
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    public function add($user, $study, string $email, string $role = null)
    {
        Gate::forUser($user)->authorize('addStudyMember', $study);

        $this->validate($study, $email, $role);

        $newStudyMember = Jetstream::findUserByEmailOrFail($email);

        AddingStudyMember::dispatch($study, $newStudyMember);

        $study->users()->attach(
            $newStudyMember, ['role' => $role]
        );
        
        StudyMemberAdded::dispatch($study, $newStudyMember);
    }

    /**
     * Validate the add member operation.
     *
     * @param  mixed  $study
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    protected function validate($study, string $email, ?string $role)
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnStudy($study, $email)
        )->validateWithBag('addStudyMember');
    }

    /**
     * Get the validation rules for adding a study member.
     *
     * @return array
     */
    protected function rules()
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
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
