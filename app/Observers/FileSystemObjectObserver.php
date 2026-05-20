<?php

namespace App\Observers;

use App\Jobs\ArchiveStudy;
use App\Models\FileSystemObject;
use App\Models\Study;
use App\Support\Studies\ResetStudyCachedState;
use Illuminate\Support\Facades\Log;

/**
 * Observe FileSystemObject changes and invalidate the affected Study's
 * pre-built archive (zip) so ArchiveStudy will rebuild it on the next run.
 *
 * ArchiveStudy short-circuits when Study::download_url is set, so anything
 * that meaningfully changes the underlying files for a study must clear that
 * URL (and best-effort delete the now-stale zip from object storage).
 */
class FileSystemObjectObserver
{
    public function __construct(private ResetStudyCachedState $resetStudyCachedState) {}

    /**
     * Structural fields whose change should invalidate the cached archive.
     *
     * Bookkeeping fields touched during ProcessDraft (is_processed, study_id,
     * dataset_id, model_type, instrument_type, external_url, integrity_*) are
     * intentionally excluded to avoid invalidating archives during routine
     * re-processing of an already-uploaded folder.
     *
     * @var array<int, string>
     */
    private const STRUCTURAL_FIELDS = [
        'name',
        'path',
        'relative_url',
        'type',
        'parent_id',
        'file_size',
        'checksum_md5',
        'checksum_sha256',
        'status',
        'is_deleted',
        'is_archived',
    ];

    /**
     * Handle the FileSystemObject "created" event.
     */
    public function created(FileSystemObject $fileSystemObject): void
    {
        $this->invalidateOwningStudy($fileSystemObject, 'created');
    }

    /**
     * Handle the FileSystemObject "updated" event.
     */
    public function updated(FileSystemObject $fileSystemObject): void
    {
        $changed = array_intersect(self::STRUCTURAL_FIELDS, array_keys($fileSystemObject->getChanges()));
        if (empty($changed)) {
            return;
        }

        $this->invalidateOwningStudy($fileSystemObject, 'updated:'.implode(',', $changed));
    }

    /**
     * Handle the FileSystemObject "deleted" event.
     */
    public function deleted(FileSystemObject $fileSystemObject): void
    {
        $this->invalidateOwningStudy($fileSystemObject, 'deleted');
    }

    /**
     * Public entry point used by code paths that delete or change FSOs in
     * ways Eloquent observers don't see (most importantly
     * FileSystemObjectService::deleteFileSystemObject, which uses a mass
     * `Builder::delete()` that intentionally bypasses model events).
     *
     * Callers are expected to invoke this BEFORE the bulk operation, while
     * the FSO subtree is still walkable from `$fso` — otherwise resolveStudy
     * would lose the parent chain and we'd silently fail to invalidate.
     */
    public function invalidateForExternalChange(FileSystemObject $fso, string $reason): void
    {
        $this->invalidateOwningStudy($fso, $reason);
    }

    /**
     * Resolve the Study that owns this FileSystemObject (if any), reset its
     * cached archive, and queue a rebuild so the next download / NMRium auto-
     * import sees the latest files.
     *
     * We intentionally do NOT short-circuit when `download_url` / `has_nmrium`
     * are already empty — a follow-up file change while an ArchiveStudy job
     * is still in flight must still enqueue a fresh dispatch, otherwise the
     * second batch of files never makes it into the archive.
     */
    private function invalidateOwningStudy(FileSystemObject $fso, string $reason): void
    {
        $study = $this->resolveStudy($fso);

        if (! $study) {
            return;
        }

        $previousUrl = $study->download_url;
        $hadNmriumImport = (bool) $study->has_nmrium;

        // Delegate the Study-side reset to the shared service so the right-
        // click "Refresh" action (ResetSampleFolder) and this observer-
        // driven path stay bit-identical. Without that, file-upload /
        // file-delete invalidations produce a different post-state than the
        // explicit reset, which is exactly what bit us before
        // (stale `internal_status` causing autoImport to race ArchiveStudy).
        $this->resetStudyCachedState->forStudy($study);

        // Clear `model_type='study'` on the sample-folder FSO so that
        // FileSystemController::processFolder re-walks it during the next
        // ProcessDraft run. Without this, processFolder skips folders that
        // already have a `model_type` set and the freshly uploaded files
        // never get re-classified into the study.
        $this->clearSampleFolderTag($study, $fso);

        Log::info('FileSystemObjectObserver: invalidated study archive', [
            'study_id' => $study->id,
            'fso_id' => $fso->id,
            'reason' => $reason,
            'previous_url' => $previousUrl,
            'previous_has_nmrium' => $hadNmriumImport,
        ]);

        $this->dispatchArchiveRebuild($study);
    }

    /**
     * Clear the `model_type='study'` tag on the FSO that anchors the study
     * (Study::fs_id), unless the change originated from that very FSO and
     * the tag has already been cleared (avoids redundant writes from the
     * recursive observer firing).
     */
    private function clearSampleFolderTag(Study $study, FileSystemObject $changed): void
    {
        if (! $study->fs_id) {
            return;
        }

        $folder = $study->fs_id === $changed->id
            ? $changed
            : FileSystemObject::find($study->fs_id);

        if (! $folder || $folder->model_type !== 'study') {
            return;
        }

        $folder->model_type = null;
        $folder->is_processed = false;
        $folder->saveQuietly();
    }

    /**
     * Dispatch ArchiveStudy for the study's project so the zip is rebuilt
     * from the current FileSystemObjects. ArchiveStudy is unique per project
     * (uniqueId = project_id, uniqueFor = 4h), so rapid-fire uploads collapse
     * into a single rebuild.
     */
    private function dispatchArchiveRebuild(Study $study): void
    {
        $project = $study->project;

        if (! $project) {
            return;
        }

        try {
            ArchiveStudy::dispatch($project);

            Log::info('embargo_publish_trace', [
                'stage' => 'file_system_object_observer_dispatch_archive_study',
                'project_id' => $project->id,
                'study_id' => $study->id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('FileSystemObjectObserver: failed to dispatch ArchiveStudy', [
                'study_id' => $study->id,
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Walk up the FileSystemObject tree (and check Study::fs_id) to find the
     * Study this object belongs to.
     */
    private function resolveStudy(FileSystemObject $fso): ?Study
    {
        if ($fso->study_id) {
            return Study::find($fso->study_id);
        }

        $study = Study::where('fs_id', $fso->id)->first();
        if ($study) {
            return $study;
        }

        $current = $fso->parent;
        $depth = 0;

        while ($current && $depth < 32) {
            if ($current->study_id) {
                return Study::find($current->study_id);
            }

            $study = Study::where('fs_id', $current->id)->first();
            if ($study) {
                return $study;
            }

            $current = $current->parent;
            $depth++;
        }

        return null;
    }
}
