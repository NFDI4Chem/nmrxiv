<?php

namespace App\Actions\Project;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Events\ProjectMemberUpdated;
use App\Models\User;
use Laravel\Jetstream\Rules\Role;

class UpdateProjectMemberRole
{
    /**
     * Update the role for the given project member.
     *
     * @param  mixed  $user
     * @param  mixed  $project
     * @param  int  $projectMemberId
     * @param  string  $role
     * @return void
     */
    public function update($user, $project, $projectMemberId, string $role)
    {
        Gate::forUser($user)->authorize('updateProjectMember', $project);

        Validator::make([
            'role' => $role,
        ], [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $project->users()->updateExistingPivot($projectMemberId, [
            'role' => $role,
        ]);

        ProjectMemberUpdated::dispatch($project->fresh(), User::find($projectMemberId));
    }
}
