<?php

namespace App\Support\Bagit;

use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
use App\Models\Dataset;
use App\Models\Study;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Backfills datasets.dataset_photo_path (and the combined
 * studies.study_photo_path array) for one study from its BagIt archive.
 *
 * This matches each dataset against the spectra listed in the bag's own
 * .nmrium file — read fresh, right now, from the archive — rather than
 * whatever is already stored in the study's `nmrium` row. Spectrum ids
 * are just a random id assigned by whichever tool last parsed the raw
 * data; an older, separately-submitted `nmrium_info` would have its own,
 * unrelated ids that can never line up with the ids in *this* bag's
 * images. Matching and image lookup therefore both stay within the same
 * bag read, and studies.nmrium is never read or written by this class.
 */
class DatasetPhotoBackfiller
{
    /**
     * @param  string|null  $folderName  The study's bag folder name (e.g. "S217"); derived from the study's identifier when omitted.
     */
    public function backfillStudy(
        Study $study,
        ?string $folderName = null,
        bool $force = false,
        bool $dryRun = false,
    ): DatasetPhotoBackfillResult {
        $result = new DatasetPhotoBackfillResult;

        $sourceDisk = Storage::disk(config('nmrxiv.spectra_parsing.storage_disk', 'local'));
        $basePath = trim(config('nmrxiv.spectra_parsing.storage_path', 'spectra_parse'), '/');
        $folderName ??= 'S'.$study->getRawOriginal('identifier');

        $study->loadMissing(['fsObject', 'draft', 'project', 'datasets.fsObject']);

        $remoteBagDir = "{$basePath}/{$folderName}";
        $archive = BagitArchive::open($sourceDisk, $remoteBagDir);

        if ($archive === null) {
            $result->skippedNoFile++;
            $result->line("  [skip] {$folderName}: no .nmrium file found under {$remoteBagDir}");

            return $result;
        }

        try {
            $contents = $archive->readNmrium();

            if ($contents === null) {
                $result->skippedNoFile++;
                $result->line("  [skip] {$folderName}: failed to read .nmrium file under {$remoteBagDir}");

                return $result;
            }

            $decoded = json_decode($contents, true);

            if (! is_array($decoded)) {
                $result->skippedNoFile++;
                $result->line("  [skip] {$folderName}: invalid JSON in .nmrium file under {$remoteBagDir}");

                return $result;
            }

            // Same envelope-unwrap as nmrxiv:backfill-study-nmrium, but kept
            // purely in memory here — never persisted to studies.nmrium.
            $nmriumInfo = $decoded['nmriumState'] ?? $decoded;

            if (! isset($nmriumInfo['data']['spectra']) || ! is_array($nmriumInfo['data']['spectra'])) {
                $result->skippedNoFile++;
                $result->line("  [skip] {$folderName}: unexpected .nmrium structure (missing data.spectra)");

                return $result;
            }

            // Collect every dataset's photo path (freshly written or already
            // present) so the study's own study_photo_path can be kept as
            // the combined array of all its datasets' photos.
            $datasetPhotoPaths = [];
            foreach ($study->datasets as $dataset) {
                $path = $this->processDataset($study, $dataset, $nmriumInfo, $archive, $force, $dryRun, $result);
                if ($path !== null) {
                    $datasetPhotoPaths[] = $path;
                }
            }

            if (! $dryRun && $datasetPhotoPaths !== []) {
                $this->updateStudyPhotoPath($study, $datasetPhotoPaths);
            }
        } finally {
            $archive->close();
        }

        return $result;
    }

    /**
     * Backfill a single dataset's photo, matching it against the bag's own
     * (freshly-read) spectra list — not the study's stored nmrium_info.
     *
     * @param  array<string, mixed>  $nmriumInfo
     * @return string|null The dataset's photo path (new or pre-existing), or null if it has none. A pre-existing path is kept when its replacement cannot be produced, so the study's combined list never drifts from the dataset's own column.
     */
    private function processDataset(
        Study $study,
        Dataset $dataset,
        array $nmriumInfo,
        BagitArchive $archive,
        bool $force,
        bool $dryRun,
        DatasetPhotoBackfillResult $result,
    ): ?string {
        if ($dataset->dataset_photo_path && ! $force) {
            $result->skippedHasPhoto++;

            return $dataset->dataset_photo_path;
        }

        // Wire the already-loaded (and already eager-loaded fsObject/draft)
        // study back onto the dataset so BioschemasHelper's own `$dataset->study`
        // lookup doesn't re-query it per dataset.
        $dataset->setRelation('study', $study);

        // Match against the bag's own spectra list, read fresh above — not
        // $study->nmrium->nmrium_info, which may hold an older, separately
        // submitted payload with unrelated spectrum ids.
        $matched = BioschemasHelper::collectStudySpectraMatchingDatasetFromPayload($dataset, $nmriumInfo);

        if ($matched === []) {
            $result->skippedNoMatch++;

            return $dataset->dataset_photo_path;
        }

        $imageBytes = null;
        foreach ($matched as $spectrum) {
            $spectrumId = $spectrum['id'] ?? null;
            if (is_string($spectrumId)) {
                $imageBytes = $archive->readImage($spectrumId);
                if ($imageBytes !== null) {
                    break;
                }
            }
        }

        if ($imageBytes === null) {
            $result->skippedNoImage++;

            return $dataset->dataset_photo_path;
        }

        if ($dryRun) {
            $result->line("  [dry-run] Would set dataset_photo_path for dataset {$dataset->identifier}");

            return null;
        }

        try {
            $path = $study->project
                ? '/projects/'.$study->project->uuid.'/'.$study->uuid.'/'.$dataset->slug.'.png'
                : '/samples/'.$study->uuid.'/'.$dataset->slug.'.png';

            // put() returns false (rather than throwing) on disks configured with throw => false.
            if (! Storage::disk(config('filesystems.default_public'))->put($path, $imageBytes, 'public')) {
                throw new RuntimeException("could not write {$path}");
            }

            $dataset->update(['dataset_photo_path' => $path]);

            $result->processed++;

            return $path;
        } catch (Throwable $e) {
            $result->failed++;
            $result->error("  [failed] dataset {$dataset->identifier}: {$e->getMessage()}");
            Log::error("Backfill dataset photo failed for dataset {$dataset->id}: {$e->getMessage()}");

            return $dataset->dataset_photo_path;
        }
    }

    /**
     * Combine every dataset photo path into the study's own study_photo_path
     * (a JSON array), only writing when it has actually changed.
     *
     * @param  list<string>  $datasetPhotoPaths
     */
    private function updateStudyPhotoPath(Study $study, array $datasetPhotoPaths): void
    {
        $combined = array_values(array_unique($datasetPhotoPaths));
        $existing = is_array($study->study_photo_path) ? $study->study_photo_path : [];

        $combinedSorted = $combined;
        $existingSorted = $existing;
        sort($combinedSorted);
        sort($existingSorted);

        if ($combinedSorted === $existingSorted) {
            return;
        }

        $study->update(['study_photo_path' => $combined]);
    }
}
