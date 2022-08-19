<?php

namespace App\Models;

trait HasProjects
{
    /**
     * User model and Projects relationship - many to many
     *
     * @var array
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * User model and Projects relationship - many to many
     *
     * @var array
     */
    public function sharedProjects()
    {
        return $this->projects()->wherePivot('role', '!=', 'creator');
    }

    /**
     * User model and Projects relationship - many to many
     *
     * @var array
     */
    public function recentProjects()
    {
        return $this->projects()->orderBy('updated_at', 'DESC');
    }

    /**
     * Determine if the user belongs to the given project.
     *
     * @param  mixed  $project
     * @return bool
     */
    public function belongsToProject($project)
    {
        if (is_null($project)) {
            return false;
        }

        return $this->hasProjectRole($project, 'creator') || $this->hasProjectRole($project, 'owner') || $this->hasProjectRole($project, 'collaborator') || $this->hasProjectRole($project, 'reviewer');
    }

    /**
     * Determine if the user is the creator the given project.
     *
     * @param  mixed  $project
     * @return bool
     */
    public function isProjectCreator($project)
    {
        if (is_null($project)) {
            return false;
        }

        return $this->id == $project->owner_id;
    }

    /**
     * Determine if the user is the owner of the given project.
     *
     * @param  mixed  $project
     * @return bool
     */
    public function ownsProject($project)
    {
        if (is_null($project)) {
            return false;
        }

        return $this->hasProjectRole($project, 'owner');
    }

    /**
     * Determine if the user can update the given project.
     *
     * @param  mixed  $project
     * @return bool
     */
    public function canUpdateProject($project)
    {
        if (is_null($project)) {
            return false;
        }

        return $this->hasProjectRole($project, 'owner') || $this->hasProjectRole($project, 'collaborator') || $this->hasProjectRole($project, 'creator');
    }

    /**
     * Determine if the user has the given role for the given project.
     *
     * @param  mixed  $project
     * @param  mixed  $role
     * @return bool
     */
    public function hasProjectRole($project, $role)
    {
        $team = $project->team ? $project->team : null;
        $id = $this->id;
        
        if (is_null($project) || is_null($role)) {
            return false;
        }

        if ($role == 'owner') {
            if ($this->isProjectCreator($project)) {
                return true;
            }
        }

        $projectUser = $project->users->first(function ($u) use ($id) {
            return $u->id === $id;
        });

        if (! is_null($projectUser)) {
            if ($projectUser->projectMembership->role == $role) {
                return true;
            } else {
                return false;
            }
        }
        
        if (! $team->personal_team) {
            $teamUser = $team->allUsers()->first(function ($u) use ($id) {
                return $u->id === $id;
            });
            if (! is_null($teamUser)) {
                $membership = $teamUser->membership ? $teamUser->membership : null;
                if($teamUser->ownsTeam($team)){
                    return true;
                }else if ($membership) {
                    if($membership->role == $role){
                        return true;
                    }
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}
