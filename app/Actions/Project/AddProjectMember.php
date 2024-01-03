<?php

namespace App\Actions\Project;

use App\Events\AddingProjectMember;
use App\Events\ProjectMemberAdded;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class AddProjectMember
{
    /**
     * Add a new project member to the given project.
     *
     * @param  mixed  $user
     * @param  mixed  $project
     * @return void
     */
    public function add($user, $project, string $email, ?string $role = null)
    {
        Gate::forUser($user)->authorize('addProjectMember', $project);

        $this->validate($project, $email, $role);

        $newProjectMember = Jetstream::findUserByEmailOrFail($email);

        AddingProjectMember::dispatch($project, $newProjectMember);

        $project->users()->attach(
            $newProjectMember, ['role' => $role]
        );

        ProjectMemberAdded::dispatch($project, $newProjectMember);
    }

    /**
     * Validate the add member operation.
     *
     * @param  mixed  $project
     * @return void
     */
    protected function validate($project, string $email, ?string $role)
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnProject($project, $email)
        )->validateWithBag('addModelMember');
    }

    /**
     * Get the validation rules for adding a project member.
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
     * Ensure that the user is not already on the project.
     *
     * @param  mixed  $project
     * @return \Closure
     */
    protected function ensureUserIsNotAlreadyOnProject($project, string $email)
    {
        return function ($validator) use ($project, $email) {
            $validator->errors()->addIf(
                $project->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the project.')
            );
        };
    }
}
