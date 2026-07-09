<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserDrafts
{
    public function __construct(
        private CreateDraft $createDraft,
    ) {}

    /**
     * Get user's drafts with files.
     */
    public function execute(User $user, bool $excludeCommunity = false): Collection
    {
        [$user_id, $team_id] = $user->getUserTeamData();

        $drafts = Draft::with([
            'Tags',
            'project:id,slug,status,draft_id',
        ])
            ->where('owner_id', $user_id)
            ->where('team_id', $team_id)
            ->where('is_deleted', false)
            ->where(function ($query) {
                $query->whereHas('files')
                    ->orWhereHas('project');
            })
            ->orderBy('updated_at', 'DESC')
            ->get();

        if (! $excludeCommunity) {
            return $drafts;
        }

        return $drafts
            ->reject(fn (Draft $draft) => $this->isCommunityDraft($draft))
            ->values();
    }

    public function isCommunityDraft(Draft $draft): bool
    {
        return $draft->isCommunityContribution();
    }

    /**
     * Find existing default draft without files for the user's current team.
     */
    public function findDefaultDraft(User $user): ?Draft
    {
        [$user_id, $team_id] = $user->getUserTeamData();

        return Draft::doesntHave('files')
            ->where('owner_id', $user_id)
            ->where('team_id', $team_id)
            ->where(function ($query) {
                $query->whereNull('settings->deposition_type')
                    ->orWhere('settings->deposition_type', '!=', Draft::DEPOSITION_COMMUNITY);
            })
            ->first();
    }

    /**
     * Get or create default draft for user.
     */
    public function getOrCreateDefaultDraft(User $user): Draft
    {
        $defaultDraft = $this->findDefaultDraft($user);

        if (! $defaultDraft) {
            $defaultDraft = $this->createDraft->execute($user);
        }

        return $defaultDraft;
    }

    /**
     * Find the user's most recent community contribution draft.
     */
    public function findCommunityDraft(User $user): ?Draft
    {
        [$user_id, $team_id] = $user->getUserTeamData();

        $draft = Draft::query()
            ->where('owner_id', $user_id)
            ->where('team_id', $team_id)
            ->where('is_deleted', false)
            ->where('settings->deposition_type', Draft::DEPOSITION_COMMUNITY)
            ->withCount('files')
            ->orderByDesc('files_count')
            ->orderByDesc('updated_at')
            ->first();

        if ($draft) {
            return $draft;
        }

        return Draft::query()
            ->where('owner_id', $user_id)
            ->where('team_id', $team_id)
            ->where('is_deleted', false)
            ->where('name', 'like', Draft::LEGACY_COMMUNITY_NAME_PREFIX.'%')
            ->withCount('files')
            ->orderByDesc('files_count')
            ->orderByDesc('updated_at')
            ->first();
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
