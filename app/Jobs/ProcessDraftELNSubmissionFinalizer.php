<?php

namespace App\Jobs;

use App\Models\Draft;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessDraftELNSubmissionFinalizer implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $draftId
    ) {}

    /**
     * Execute the job - Final processing after ProcessProjectSpectra completes.
     */
    public function handle(): void
    {
        $draft = Draft::find($this->draftId);

        if (! $draft) {
            Log::error("Draft not found for finalizer: {$this->draftId}");

            return;
        }

        try {
            Log::info('Starting final processing after ProcessProjectSpectra', [
                'draft_id' => $draft->id,
            ]);

            $project = $draft->project;
            if (! $project) {
                Log::error('No project found for draft in finalizer', ['draft_id' => $draft->id]);

                return;
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

                ProcessSubmission::dispatch($project);
            } else {
                Log::error('Validation failed for project', [
                    'project_id' => $project->id,
                    'draft_id' => $draft->id,
                ]);
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
}
