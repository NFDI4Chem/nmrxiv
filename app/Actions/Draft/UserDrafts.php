<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserDrafts
{
    /**
     * Get user's drafts with files.
     */
    public function execute(User $user): Collection
    {
        [$user_id, $team_id] = $user->getUserTeamData();

        return Draft::with('Tags')
            ->where('owner_id', $user_id)
            ->where('team_id', $team_id)
            ->where('is_deleted', false)
            ->where(function ($query) {
                $query->whereHas('files')
                    ->orWhereHas('project');
            })
            ->orderBy('updated_at', 'DESC')
            ->get();
    }

    /**
     * Find existing default draft without files.
     */
    public function findDefaultDraft(User $user): ?Draft
    {
        [$user_id, $team_id] = $user->getUserTeamData();

        return Draft::doesntHave('files')
            ->where('owner_id', $user_id)
            ->first();
    }

    /**
     * Get or create default draft for user.
     */
    public function getOrCreateDefaultDraft(User $user): Draft
    {
        $defaultDraft = $this->findDefaultDraft($user);

        if (! $defaultDraft) {
            $createDraft = new CreateDraft;
            $defaultDraft = $createDraft->execute($user);
        }

        return $defaultDraft;
    }

    /**
     * Get shared drafts for user.
     */
    public function getSharedDrafts(User $user)
    {
        // TODO: Implement sharedDrafts() method on User model or handle differently
        // For now, return empty collection to avoid undefined method error
        try {
            return $user->sharedDrafts();
        } catch (\BadMethodCallException $e) {
            return collect();
        }
    }
}
