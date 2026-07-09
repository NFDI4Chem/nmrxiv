<?php

namespace App\Policies;

use App\Models\Draft;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DraftPolicy
{
    use HandlesAuthorization;

    public function updateDraft(User $user, Draft $draft): bool
    {
        [$user_id] = $user->getUserTeamData();

        return $draft->owner_id === $user_id;
    }
}
