<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return mixed
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function update(User $user, Team $team): bool
    {
        if ($user->hasTeamRole($user->currentTeam->fresh(), 'owner') || $user->ownsTeam($team)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can add team members.
     *
     * @return mixed
     */
    public function addTeamMember(User $user, Team $team)
    {
        if ($user->hasTeamRole($user->currentTeam->fresh(), 'owner') || $user->ownsTeam($team)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update team member permissions.
     *
     * @return mixed
     */
    public function updateTeamMember(User $user, Team $team)
    {
        if ($user->hasTeamRole($user->currentTeam->fresh(), 'owner') || $user->ownsTeam($team)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can remove team members.
     *
     * @return mixed
     */
    public function removeTeamMember(User $user, Team $team)
    {
        if ($user->hasTeamRole($team, 'owner') || $user->ownsTeam($team)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->ownsTeam($team);
    }
}
