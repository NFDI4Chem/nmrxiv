<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function viewProject(User $user, Project $project)
    {
        if ($project->is_public) {
            return true;
        } else {
            if (is_null($user)) {
                return false;
            } else {
                return $user->belongsToProject($project);
            }
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function createProject(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function updateProject(User $user, Project $project)
    {
        if ($project->is_public || $project->is_archived || $project->is_deleted || $project->is_published) {
            return false;
        }

        return $user->canUpdateProject($project);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function deleteProject(User $user, Project $project)
    {
        return $user->isProjectCreator($project);
    }

    /**
     * Determine whether the user can add project members.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function addProjectMember(User $user, Project $project)
    {
        return $user->ownsProject($project);
    }

    /**
     * Determine whether the user can update project member permissions.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function updateProjectMember(User $user, Project $project)
    {
        return $user->ownsProject($project);
    }

    /**
     * Determine whether the user can remove project members.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function removeProjectMember(User $user, Project $project)
    {
        return $user->ownsProject($project);
    }
}
