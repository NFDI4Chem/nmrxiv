<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\User;

class GetOrCreateCommunityDraft
{
    public function __construct(
        private UserDrafts $userDrafts,
        private CreateDraft $createDraft,
        private DetachStudyFilesystemFromDraft $detachStudyFilesystemFromDraft,
    ) {}

    public function execute(User $user): Draft
    {
        $draft = $this->userDrafts->findCommunityDraft($user);

        if ($draft) {
            $draft = $this->ensureCommunityDepositionType($draft);
            $this->detachStudyFilesystemFromDraft->detachSubmittedStudies($draft);

            return $draft;
        }

        $draft = $this->createDraft->execute($user, [
            'deposition_type' => Draft::DEPOSITION_COMMUNITY,
        ]);

        return $draft->fresh();
    }

    private function ensureCommunityDepositionType(Draft $draft): Draft
    {
        $settings = $draft->settings ?? [];

        if (($settings['deposition_type'] ?? null) === Draft::DEPOSITION_COMMUNITY) {
            return $draft;
        }

        $settings['deposition_type'] = Draft::DEPOSITION_COMMUNITY;
        $draft->update(['settings' => $settings]);

        return $draft->fresh();
    }
}
