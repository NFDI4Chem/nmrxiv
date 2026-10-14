<?php

namespace App\Policies;

use App\Actions\Draft\ProcessDraft;
use App\Models\Draft;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DraftPolicy
{
    use HandlesAuthorization;

    public function updateDraft(User $user, Draft $draft): bool
    {
        [$user_id] = $user->getUserTeamData();

        if ($draft->owner_id === $user_id) {
            return true;
        }

        $project = app(ProcessDraft::class)->resolveDraftProject($draft);

        return $project !== null && $user->canUpdateProject($project);
    }
}
