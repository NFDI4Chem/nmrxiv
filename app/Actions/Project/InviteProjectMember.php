<?php

namespace App\Actions\Project;

use App\Events\InvitingProjectMember;
use App\Mail\ProjectInvitation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class InviteProjectMember
{
    /**
     * Invite a new project member to the given project.
     *
     * @param  mixed  $user
     * @param  mixed  $project
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    public function invite($user, $project, string $email, string $role = null, string $message = null)
    {
        // Gate::forUser($user)->authorize('addProjectMember', $project);

        $this->validate($project, $email, $role, $message);

        InvitingProjectMember::dispatch($project, $email, $role, $message);

        $invitation = $project->projectInvitations()->create([
            'email' => $email,
            'role' => $role,
            'message' => $message,
            'invited_by' => $user->name,
        ]);

        Mail::to($email)->send(new ProjectInvitation($invitation));
    }

    /**
     * Validate the invite member operation.
     *
     * @param  mixed  $project
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    protected function validate($project, string $email, ?string $role, ?string $message)
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
            'message' => $message,
        ], $this->rules($project), [
            'email.unique' => __('This user has already been invited to the project.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnProject($project, $email)
        )->validateWithBag('addProjectMember');
    }

    /**
     * Get the validation rules for inviting a project member.
     *
     * @param  mixed  $project
     * @return array
     */
    protected function rules($project)
    {
        return array_filter([
            'email' => ['required', 'email', Rule::unique('project_invitations')->where(function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })],
            'role' => Jetstream::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the project.
     *
     * @param  mixed  $project
     * @param  string  $email
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
