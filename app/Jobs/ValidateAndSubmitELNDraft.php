<?php

namespace App\Jobs;

use App\Models\Draft;
use App\Services\ChemotionRepositoryTrackerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ValidateAndSubmitELNDraft implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $draftId
    ) {}

    /**
     * Execute the job - Final processing after ProcessELNSpectra completes.
     */
    public function handle(): void
    {
        $draft = Draft::find($this->draftId);

        if (! $draft) {
            Log::error("Draft not found for finalizer: {$this->draftId}");

            return;
        }

        try {
            Log::info('Starting final processing after ProcessELNSpectra', [
                'draft_id' => $draft->id,
            ]);

            $project = $draft->project;
            if (! $project) {
                Log::error('No project found for draft in finalizer', ['draft_id' => $draft->id]);

                return;
            }

            $studies = $project->studies;
            foreach ($studies as $study) {
                $study->submitted_through = $draft->eln;
                $study->external_id = $draft->external_id;
                $study->processing_logs = $draft->processing_logs;
                $study->save();
            }

            // Set project properties
            $draft->project_enabled = false;
            $draft->save();

            $project->release_date = $draft->release_date;
            $project->status = 'queued';
            $project->save();

            // Process validation
            $validation = $project->validation;
            $validation->process();
            $validation = $validation->fresh();

            $status = true;

            $draft->update([
                'status' => 'PUBLISHING',
                'current_step' => '3',
            ]);

            // Check validation status for all studies
            foreach ($validation['report']['project']['studies'] as $study) {
                if (! $study['status']) {
                    $status = false;
                }
            }

            // Dispatch ProcessSubmission if validation passes
            if ($status) {
                Log::info('Validation passed for project, dispatching ProcessSubmission', [
                    'project_id' => $project->id,
                    'draft_id' => $draft->id,
                ]);

                // Track submission as validated and ready for publishing
                $this->trackSubmissionValidated($draft, $project, $project->owner);

                ProcessSubmission::dispatch($project);
            } else {
                Log::error('Validation failed for project', [
                    'project_id' => $project->id,
                    'draft_id' => $draft->id,
                ]);

                // Track validation failure
                $this->trackValidationFailed($draft, $project);
            }

        } catch (\Exception $e) {
            Log::error('Failed to finalize ELN submission processing', [
                'draft_id' => $draft->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Track submission as validated and ready for publishing
     */
    private function trackSubmissionValidated(Draft $draft, $project, $owner): void
    {
        try {
            $trackerService = app(ChemotionRepositoryTrackerService::class);

            // Check if tracking is enabled
            if (! $trackerService->isEnabled()) {
                Log::debug('Chemotion tracking is disabled, skipping tracking for validated submission', [
                    'external_id' => $draft->external_id,
                    'project_id' => $project->id,
                ]);

                return;
            }

            $metadata = [
                'validation_status' => 'passed',
                'project_id' => $project->id,
                'studies_count' => $project->studies->count(),
                'validated_at' => now()->toISOString(),
                'ready_for_publishing' => true,
                'owner_name' => $owner->first_name.' '.$owner->last_name,
                'owner_email' => $owner->email,
            ];

            $trackerService->updateElnSubmissionStatus(
                submissionId: $draft->external_id,
                newStatus: ChemotionRepositoryTrackerService::STATUS_PROCESSED,
                additionalMetadata: $metadata
            );

            Log::info('Chemotion tracking updated for validated submission', [
                'external_id' => $draft->external_id,
                'project_id' => $project->id,
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to update Chemotion tracking for validated submission', [
                'external_id' => $draft->external_id,
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track validation failure
     */
    private function trackValidationFailed(Draft $draft, $project): void
    {
        try {
            $trackerService = app(ChemotionRepositoryTrackerService::class);

            // Check if tracking is enabled
            if (! $trackerService->isEnabled()) {
                Log::debug('Chemotion tracking is disabled, skipping tracking for failed validation', [
                    'external_id' => $draft->external_id,
                    'project_id' => $project->id,
                ]);

                return;
            }

            $metadata = [
                'validation_status' => 'failed',
                'project_id' => $project->id,
                'validation_failed_at' => now()->toISOString(),
            ];

            $trackerService->updateElnSubmissionStatus(
                submissionId: $draft->external_id,
                newStatus: ChemotionRepositoryTrackerService::STATUS_REJECTED,
                additionalMetadata: $metadata
            );

            Log::info('Chemotion tracking updated for failed validation', [
                'external_id' => $draft->external_id,
                'project_id' => $project->id,
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to update Chemotion tracking for failed validation', [
                'external_id' => $draft->external_id,
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
