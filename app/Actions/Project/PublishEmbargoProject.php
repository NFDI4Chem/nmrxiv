<?php

namespace App\Actions\Project;

use App\Exceptions\EmbargoPublicationFailed;
use App\Jobs\ProcessSubmission;
use App\Models\Project;
use App\Models\Validation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PublishEmbargoProject
{
    /**
     * Queue (or immediately process) the project for publication based on its release date.
     *
     * When a draft exists, publishing typically includes moving files and deleting the draft,
     * so we dispatch asynchronously. When there is no draft, we dispatch synchronously so a
     * "publish now" path can take effect immediately.
     *
     * @return array{hasDraft: bool, dispatched: 'sync'|'async', validation: Validation}
     */
    public function publish(Project $project, bool $restoreReleaseDateOnValidationFailure = false): array
    {
        if ($project->is_public) {
            throw ValidationException::withMessages([
                'publish' => 'Project is already public.',
            ]);
        }

        if ($project->is_archived) {
            throw ValidationException::withMessages([
                'publish' => 'Archived projects cannot be published.',
            ]);
        }

        if ($project->status !== 'embargo') {
            throw ValidationException::withMessages([
                'publish' => 'Project is not in embargo status.',
            ]);
        }

        if ($project->doi === null || $project->doi === '') {
            throw ValidationException::withMessages([
                'publish' => 'A DOI is required before publishing this project.',
            ]);
        }

        $validation = $this->validateForPublication($project, $restoreReleaseDateOnValidationFailure);
        $releaseDate = now()->startOfDay()->toDateString();
        $hasDraft = $project->draft_id !== null;

        DB::transaction(function () use ($project, $releaseDate) {
            if ($releaseDate !== null) {
                $project->release_date = $releaseDate;
            }

            $project->status = 'queued';
            $project->save();
        });

        Log::info('embargo_publish_trace', [
            'stage' => 'publish_embargo_project_action_dispatch_process_submission',
            'project_id' => $project->id,
            'release_date' => $project->release_date,
            'status' => $project->status,
            'draft_id' => $project->draft_id,
            'dispatch' => $hasDraft ? 'async' : 'sync',
        ]);

        if ($hasDraft) {
            ProcessSubmission::dispatch($project);

            return [
                'hasDraft' => true,
                'dispatched' => 'async',
                'validation' => $validation,
            ];
        }

        ProcessSubmission::dispatchSync($project);

        return [
            'hasDraft' => false,
            'dispatched' => 'sync',
            'validation' => $validation,
        ];
    }

    private function validateForPublication(Project $project, bool $restoreReleaseDateOnValidationFailure): Validation
    {
        $validation = $project->validation;

        if (! $validation) {
            throw ValidationException::withMessages([
                'publish' => 'Project validation not found. Please ensure the project is properly configured.',
            ]);
        }

        $originalReleaseDate = $project->release_date;

        $project->release_date = now()->startOfDay()->toDateString();
        $project->save();

        $validation->process();
        $publishAttemptValidation = $validation->fresh();

        if (! $publishAttemptValidation['report']['project']['status']) {
            if ($restoreReleaseDateOnValidationFailure) {
                $project->release_date = $originalReleaseDate;
                $project->save();
                $project->refresh();

                if ($project->validation) {
                    $project->validation->process();
                }
            }

            throw new EmbargoPublicationFailed(
                $project,
                'Validation failing. Please provide all the required data and try again. If the problem persists, please contact us at info.nmrxiv@uni-jena.de',
                $publishAttemptValidation,
            );
        }

        return $publishAttemptValidation;
    }
}
