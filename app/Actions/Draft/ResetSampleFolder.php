<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Study;
use App\Support\Studies\ResetStudyCachedState;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Reset cached state for a single sample folder (study-tagged FSO) on a draft.
 *
 * "Reset" clears the things that make Step 1 / Step 2 short-circuit on stale
 * data so the next "Proceed to Step 2" run treats the folder as fresh:
 *   - The sample-folder FSO and every descendant lose `model_type`,
 *     `instrument_type` and `is_processed` so FileSystemController::process-
 *     Folder re-walks the entire subtree and re-tags from scratch. Without
 *     clearing the descendants too, the Bruker / Varian / JCAMP icons stay
 *     visible after a page reload because their `instrument_type` is still
 *     persisted.
 *   - The associated Study has its cached spectra/archive state reset via
 *     the shared {@see ResetStudyCachedState} service (download_url,
 *     has_nmrium, internal_status, dataset has_nmrium, NMRium rows, plus a
 *     best-effort cleanup of the cached zip in object storage). Sharing the
 *     service with the FileSystemObject observer keeps the right-click
 *     "Refresh" path and the file-upload / file-delete paths bit-identical.
 *
 * No ArchiveStudy is dispatched here — Step 2's ProcessDraft → publish →
 * ProcessSubmission flow is the source of truth that re-tags and re-archives.
 */
class ResetSampleFolder
{
    /**
     * Cap on how deep we descend when clearing tags on the FSO subtree.
     * Mirrors the upper bound used by FileSystemObjectObserver.
     */
    private const MAX_DEPTH = 32;

    public function __construct(private ResetStudyCachedState $resetStudyCachedState) {}

    /**
     * Reset the sample folder identified by $folder on $draft.
     *
     * @return array{ok: bool, study_id: int|null, message: string}
     */
    public function execute(Draft $draft, FileSystemObject $folder): array
    {
        if ($folder->draft_id !== $draft->id) {
            return [
                'ok' => false,
                'study_id' => null,
                'message' => 'Filesystem object does not belong to this draft.',
            ];
        }

        if ($folder->model_type !== 'study') {
            return [
                'ok' => false,
                'study_id' => null,
                'message' => 'Only sample folders can be reset.',
            ];
        }

        $study = $this->resolveStudy($folder);
        $studyId = $study?->id;

        DB::transaction(function () use ($folder, $study): void {
            $this->clearSubtreeTags($folder);

            if ($study) {
                $this->resetStudyCachedState->forStudy($study);
            }
        });

        Log::info('ResetSampleFolder: reset sample folder', [
            'draft_id' => $draft->id,
            'fso_id' => $folder->id,
            'study_id' => $studyId,
        ]);

        return [
            'ok' => true,
            'study_id' => $studyId,
            'message' => 'Sample folder reset. The next "Proceed to Step 2" run will reprocess it.',
        ];
    }

    /**
     * Recursively clear `model_type`, `instrument_type` and `is_processed`
     * on the sample folder and every descendant so the next ProcessDraft
     * run re-walks and re-tags the entire subtree from scratch.
     *
     * Uses `saveQuietly()` to bypass FileSystemObjectObserver, which would
     * otherwise re-trigger archive invalidation work and dispatch an
     * ArchiveStudy job we explicitly want to defer to Step 2.
     */
    private function clearSubtreeTags(FileSystemObject $folder, int $depth = 0): void
    {
        if ($depth >= self::MAX_DEPTH) {
            return;
        }

        $changed = false;
        if ($folder->model_type !== null) {
            $folder->model_type = null;
            $changed = true;
        }
        if ($folder->instrument_type !== null) {
            $folder->instrument_type = null;
            $changed = true;
        }
        if ($folder->is_processed) {
            $folder->is_processed = false;
            $changed = true;
        }
        if ($changed) {
            $folder->saveQuietly();
        }

        foreach ($folder->children as $child) {
            $this->clearSubtreeTags($child, $depth + 1);
        }
    }

    /**
     * Resolve the Study attached to a sample-folder FSO.
     */
    private function resolveStudy(FileSystemObject $folder): ?Study
    {
        if ($folder->study_id) {
            return Study::find($folder->study_id);
        }

        return Study::where('fs_id', $folder->id)->first();
    }
}
