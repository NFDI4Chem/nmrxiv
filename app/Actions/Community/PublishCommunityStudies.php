<?php

namespace App\Actions\Community;

use App\Actions\Draft\DetachStudyFilesystemFromDraft;
use App\Jobs\ProcessSubmission;
use App\Models\Draft;
use App\Models\License;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PublishCommunityStudies
{
    public function __construct(
        private DetachStudyFilesystemFromDraft $detachStudyFilesystemFromDraft,
    ) {}

    /**
     * Queue selected community draft studies for independent public release.
     *
     * @param  array<int, int>  $studyIds
     * @return array{study_ids: array<int, int>}
     */
    public function execute(Draft $draft, array $studyIds): array
    {
        if (! $draft->isCommunityContribution()) {
            throw ValidationException::withMessages([
                'draft' => 'This action is only available for community contribution drafts.',
            ]);
        }

        $project = Project::query()->where('draft_id', $draft->id)->first();

        if (! $project) {
            throw ValidationException::withMessages([
                'study_ids' => 'Process your uploaded files before submitting samples.',
            ]);
        }

        $studies = $project->studies()
            ->with(['datasets', 'sample.molecules'])
            ->where('draft_id', $draft->id)
            ->whereIn('id', $studyIds)
            ->get();

        if ($studies->count() !== count($studyIds)) {
            throw ValidationException::withMessages([
                'study_ids' => 'One or more selected samples are invalid.',
            ]);
        }

        $notReady = $studies->filter(fn (Study $study) => ! $study->isReadyForCommunityPublish());

        if ($notReady->isNotEmpty()) {
            throw ValidationException::withMessages([
                'study_ids' => 'One or more selected samples are not ready to publish. Ensure processing is complete, NMRium data is present, and a structure is assigned.',
            ]);
        }

        $this->applyCommunityLicense($project, $studies);

        $queuedIds = $studies->pluck('id')->map(fn ($id) => (int) $id)->all();

        DB::transaction(function () use ($draft, $project, $queuedIds): void {
            Study::query()
                ->whereIn('id', $queuedIds)
                ->where('draft_id', $draft->id)
                ->lockForUpdate()
                ->get();

            $draft->project_enabled = false;
            $draft->save();

            $project->release_date = now()->startOfDay()->toDateString();
            $project->status = 'queued';
            $project->save();

            Study::query()
                ->whereIn('id', $queuedIds)
                ->update([
                    'internal_status' => 'processing',
                    'draft_id' => null,
                ]);

            $this->detachStudyFilesystemFromDraft->execute($draft, $queuedIds);
        });

        ProcessSubmission::dispatch($project->fresh(), $queuedIds, preserveDraft: true);

        return ['study_ids' => $queuedIds];
    }

    private function applyCommunityLicense(Project $project, Collection $studies): void
    {
        $license = License::query()->where('spdx_id', 'CC0-1.0')->first();

        if ($license === null) {
            throw ValidationException::withMessages([
                'license' => 'The default community license (CC0) is not configured. Please contact support.',
            ]);
        }

        if ($project->license_id === null) {
            $project->license_id = $license->id;
            $project->save();
        }

        $licenseId = $project->license_id;

        foreach ($studies as $study) {
            $study->license_id = $licenseId;
            $study->save();

            foreach ($study->datasets as $dataset) {
                if ($dataset->license_id === null) {
                    $dataset->license_id = $licenseId;
                    $dataset->save();
                }
            }
        }
    }
}
