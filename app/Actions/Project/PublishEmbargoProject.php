<?php

namespace App\Actions\Project;

use App\Jobs\ProcessSubmission;
use App\Models\Project;
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
     * @return array{hasDraft: bool, dispatched: 'sync'|'async'}
     */
    public function publish(Project $project): array
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
            ];
        }

        ProcessSubmission::dispatchSync($project);

        return [
            'hasDraft' => false,
            'dispatched' => 'sync',
        ];
    }
}
