<?php

namespace App\Models;

trait HasStudies
{
    /**
     * User model and Studies relationship - many to many
     *
     * @var array
     */
    public function studies()
    {
        return $this->belongsToMany(Study::class);
    }

    /**
     * User model and Studies relationship - many to many
     *
     * @var array
     */
    public function sharedStudies()
    {
        return $this->studies()->wherePivot('role', '!=', 'creator');
    }

    /**
     * User model and Studies relationship - many to many
     *
     * @var array
     */
    public function recentStudies()
    {
        return $this->studies()->orderBy('updated_at', 'DESC');
    }

    /**
     * Determine if the user belongs to the given study.
     *
     * @param  mixed  $study
     * @return bool
     */
    public function belongsToStudy($study)
    {
        if (is_null($study)) {
            return false;
        }

        return $this->hasStudyRole($study, 'creator') || $this->hasStudyRole($study, 'owner') || $this->hasStudyRole($study, 'collaborator') || $this->hasStudyRole($study, 'reviewer');
    }

    /**
     * Determine if the user is the creator the given study.
     *
     * @param  mixed  $study
     * @return bool
     */
    public function isStudyCreator($study)
    {
        if (is_null($study)) {
            return false;
        }

        return $this->id == $study->owner_id;
    }

    /**
     * Determine if the user is the owner of the given study.
     *
     * @param  mixed  $study
     * @return bool
     */
    public function ownsStudy($study)
    {
        if (is_null($study)) {
            return false;
        }

        return $this->hasStudyRole($study, 'owner');
    }

    /**
     * Determine if the user can update the given study.
     *
     * @param  mixed  $study
     * @return bool
     */
    public function canUpdateStudy($study)
    {
        if (is_null($study)) {
            return false;
        }

        return $this->hasStudyRole($study, 'owner') || $this->hasStudyRole($study, 'collaborator');
    }

    /**
     * Determine if the user has the given role for the given study.
     *
     * @param  mixed  $study
     * @param  mixed  $role
     * @return bool
     */
    public function hasStudyRole($study, $role)
    {
        $team = $study->team ? $study->team : null;
        $id = $this->id;

        if (is_null($study) || is_null($role)) {
            return false;
        }

        if ($role == 'owner') {
            if ($this->isStudyCreator($study)) {
                return true;
            }
        }

        $studyUser = $study->users->first(function ($u) use ($id) {
            return $u->id === $id;
        });

        if (! is_null($studyUser)) {
            if ($studyUser->studyMembership->role == $role) {
                return true;
            } else {
                return false;
            }
        }

        $projectUser = $study->project->users->first(function ($u) use ($id) {
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
                if ($teamUser->ownsTeam($team)) {
                    return true;
                } elseif ($membership) {
                    if ($membership->role == $role) {
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
