<?php

namespace App\Actions\Project;

use App\Events\ProjectMemberRemoved;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RemoveProjectMember
{
    /**
     * Remove the project member from the given project.
     *
     * @param  mixed  $user
     * @param  mixed  $project
     * @param  mixed  $projectMember
     * @return void
     */
    public function remove($user, $project, $projectMember)
    {
        $this->authorize($user, $project, $projectMember);

        $this->ensureUserDoesNotOwnProject($projectMember, $project);

        $project->removeUser($projectMember);

        ProjectMemberRemoved::dispatch($project, $projectMember);

        return redirect()->route('dashboard')->with('success', 'Member removed successfully');
    }

    /**
     * Authorize that the user can remove the project member.
     *
     * @param  mixed  $user
     * @param  mixed  $project
     * @param  mixed  $projectMember
     * @return void
     */
    protected function authorize($user, $project, $projectMember)
    {
        if (! Gate::forUser($user)->check('removeProjectMember', $project) &&
            $user->id !== $projectMember->id) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the project.
     *
     * @param  mixed  $projectMember
     * @param  mixed  $project
     * @return void
     */
    protected function ensureUserDoesNotOwnProject($projectMember, $project)
    {
        if ($projectMember->id === $project->owner->id) {
            throw ValidationException::withMessages([
                'project' => [__('You may not leave a project that you created.')],
            ])->errorBag('removeProjectMember');
        }
    }
}
