<?php

namespace App\Support\Studies;

use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Study;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Single source of truth for "reset everything cached about this study" used
 * by both the right-click "Refresh" action (ResetSampleFolder) and the
 * FileSystemObject observer (file create/update/delete invalidation path).
 *
 * What gets cleared (and why):
 *  - `download_url` on the Study, plus a best-effort delete of the cached
 *    zip in object storage. ArchiveStudy short-circuits when this URL is
 *    set, so it MUST be cleared for the rebuild to actually run.
 *  - `has_nmrium` on the Study. The upload page's autoImport flow only
 *    re-fetches NMRium data when this flag is false.
 *  - `internal_status` on the Study. Upload.vue's polling loop waits for
 *    every study to report 'complete' before calling autoImport. Leaving
 *    a stale 'complete' marker here is exactly what produces the
 *    "spectra processed before archive" race.
 *  - `is_archived` on the Study. A study that was previously soft-archived
 *    (hidden from public listings) must be unflagged when its underlying
 *    files change, otherwise the rebuilt content would remain hidden.
 *  - `has_nmrium` on every dataset under the study (same reasoning as the
 *    study-level flag).
 *  - All `NMRium` polymorphic rows for the study and its datasets, so the
 *    next import writes fresh rows instead of versioning over stale ones.
 *
 * Idempotent: every step is a no-op once the underlying state is already
 * cleared, which makes it safe to call from a per-row observer firing on a
 * bulk delete.
 */
class ResetStudyCachedState
{
    /**
     * Reset cached state for a single study.
     */
    public function forStudy(Study $study): void
    {
        $previousUrl = $study->download_url;

        if (! empty($previousUrl)) {
            $this->deleteArchiveFromStorage($study, $previousUrl);
        }

        $needsStudySave = $previousUrl !== null
            || (bool) $study->has_nmrium
            || $study->internal_status !== null
            || (bool) $study->is_archived;

        if ($needsStudySave) {
            $study->download_url = null;
            $study->has_nmrium = false;
            $study->internal_status = null;
            $study->is_archived = false;
            $study->saveQuietly();
        }

        $datasetIds = $study->datasets()->pluck('id')->all();

        if (! empty($datasetIds)) {
            Dataset::whereIn('id', $datasetIds)
                ->where('has_nmrium', true)
                ->update(['has_nmrium' => false]);

            NMRium::where('nmriumable_type', Dataset::class)
                ->whereIn('nmriumable_id', $datasetIds)
                ->delete();
        }

        NMRium::where('nmriumable_type', Study::class)
            ->where('nmriumable_id', $study->id)
            ->delete();
    }

    /**
     * Best-effort deletion of the cached zip from object storage. Failures
     * are logged but never raised — the in-DB reset has already succeeded
     * and the zip will be overwritten on the next ArchiveStudy run anyway.
     */
    private function deleteArchiveFromStorage(Study $study, string $url): void
    {
        $key = $this->extractStorageKeyFromUrl($url);

        if ($key === null) {
            return;
        }

        try {
            $disk = config('filesystems.default');
            if (Storage::disk($disk)->exists($key)) {
                Storage::disk($disk)->delete($key);
            }
        } catch (\Throwable $e) {
            Log::warning('ResetStudyCachedState: failed to delete archive', [
                'study_id' => $study->id,
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Strip the "{endpoint}/{bucket}/" prefix from a download URL to recover
     * the storage key used by the configured disk.
     */
    private function extractStorageKeyFromUrl(string $url): ?string
    {
        $disk = config('filesystems.default');
        $bucket = config("filesystems.disks.{$disk}.bucket");

        if (! $bucket) {
            return null;
        }

        $marker = '/'.$bucket.'/';
        $position = strpos($url, $marker);

        if ($position === false) {
            return null;
        }

        $key = substr($url, $position + strlen($marker));

        return $key !== '' ? $key : null;
    }
}
