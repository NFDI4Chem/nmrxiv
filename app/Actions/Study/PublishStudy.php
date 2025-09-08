<?php

namespace App\Actions\Study;

use App\Models\Study;
use App\Services\ChemotionRepositoryTrackerService;
use Illuminate\Support\Facades\Log;

class PublishStudy
{
    /**
     * Publish the given study.
     *
     * @param  mixed  $study
     * @return void
     */
    public function publish($study)
    {
        $study->is_public = true;
        $study->save();
        $datasets = $study->datasets;
        foreach ($datasets as $dataset) {
            $dataset->is_public = true;
            $dataset->save();
        }

        // Track publication if this is an ELN submission
        $this->trackStudyPublished($study);
    }

    /**
     * Track study publication in Chemotion Repository-Tracker
     */
    private function trackStudyPublished(Study $study): void
    {
        // Only track if this study came from an ELN submission
        if (! $study->tracking_item_name || ! $study->submitted_through) {
            return;
        }

        try {
            $trackerService = app(ChemotionRepositoryTrackerService::class);

            // Check if tracking is enabled
            if (! $trackerService->isEnabled()) {
                Log::debug('Chemotion tracking is disabled, skipping tracking for published study', [
                    'tracking_item_name' => $study->tracking_item_name,
                    'study_id' => $study->id,
                ]);

                return;
            }

            $metadata = [
                'study_id' => $study->id,
                'study_identifier' => $study->identifier,
                'study_name' => $study->name,
                'study_uuid' => $study->uuid,
                'datasets_count' => $study->datasets->count(),
                'published_at' => now()->toISOString(),
                'public_url' => $study->public_url,
                'doi' => $study->doi,
            ];

            $trackerService->updateElnSubmissionStatus(
                submissionId: $study->tracking_item_name,
                newStatus: ChemotionRepositoryTrackerService::STATUS_PUBLISHED,
                additionalMetadata: $metadata,
                ownerName: $study->owner->first_name.' '.$study->owner->last_name,
                ownerEmail: $study->owner->email,
            );

            Log::info('Chemotion tracking updated for published study', [
                'tracking_item_name' => $study->tracking_item_name,
                'study_id' => $study->id,
                'study_identifier' => $study->identifier,
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to update Chemotion tracking for published study', [
                'tracking_item_name' => $study->tracking_item_name,
                'study_id' => $study->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
