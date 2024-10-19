<?php

namespace App\Policies;

use App\Models\Study;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudyPolicy
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
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function viewStudy(User $user, Study $study)
    {
        if (is_null($user) && $study->is_public) {
            return true;
        }

        return $user->belongsToStudy($study);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function createStudy(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return mixed
     */
    public function updateStudy(User $user, Study $study)
    {
        if ($study->is_archived || $study->is_deleted) {
            return false;
        }

        return $user->canUpdateStudy($study);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function deleteStudy(User $user, Study $study)
    {
        return $user->isStudyCreator($study);
    }

    /**
     * Determine whether the user can add study members.
     *
     * @return mixed
     */
    public function addStudyMember(User $user, Study $study)
    {
        return $user->ownsStudy($study);
    }

    /**
     * Determine whether the user can update study member permissions.
     *
     * @return mixed
     */
    public function updateStudyMember(User $user, Study $study)
    {
        return $user->ownsStudy($study);
    }

    /**
     * Determine whether the user can remove study members.
     *
     * @return mixed
     */
    public function removeStudyMember(User $user, Study $study)
    {
        return $user->ownsStudy($study);
    }
}
