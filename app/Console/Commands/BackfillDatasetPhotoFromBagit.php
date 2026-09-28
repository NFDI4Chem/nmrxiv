<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
use App\Models\Dataset;
use App\Models\Study;
use App\Support\Bagit\BagitArchive;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BackfillDatasetPhotoFromBagit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:backfill-dataset-photo
                            {--ids= : Comma-separated study folder names (e.g. S1,S100) to process}
                            {--limit= : Limit number of study folders to process}
                            {--force : Overwrite dataset_photo_path even if already set}
                            {--dry-run : Report what would happen without writing any changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Backfill datasets.dataset_photo_path (and the combined studies.study_photo_path array) from a study's BagIt archive";

    private int $processed = 0;

    private int $skippedHasPhoto = 0;

    private int $skippedStudyNotFound = 0;

    private int $skippedNoFile = 0;

    private int $skippedNoMatch = 0;

    private int $skippedNoImage = 0;

    private int $failed = 0;

    /**
     * Execute the console command.
     *
     * This matches each dataset against the spectra listed in the bag's own
     * .nmrium file — read fresh, right now, from the archive — rather than
     * whatever is already stored in the study's `nmrium` row. Spectrum ids
     * are just a random id assigned by whichever tool last parsed the raw
     * data; an older, separately-submitted `nmrium_info` would have its own,
     * unrelated ids that can never line up with the ids in *this* bag's
     * images. Matching and image lookup therefore both stay within the same
     * bag read, and studies.nmrium is never read or written by this command.
     */
    public function handle(): int
    {
        $sourceDisk = Storage::disk(config('nmrxiv.spectra_parsing.storage_disk', 'local'));
        $basePath = trim(config('nmrxiv.spectra_parsing.storage_path', 'spectra_parse'), '/');

        $folders = collect($sourceDisk->directories($basePath))
            ->map(fn (string $path) => basename($path))
            ->filter(fn (string $name) => preg_match('/^S\d+$/i', $name) === 1)
            ->values();

        if ($ids = $this->option('ids')) {
            $wanted = array_map(fn (string $id) => strtoupper(trim($id)), explode(',', $ids));
            $folders = $folders->filter(fn (string $name) => in_array(strtoupper($name), $wanted, true))->values();
        }

        if ($limit = $this->option('limit')) {
            $folders = $folders->take((int) $limit);
        }

        if ($folders->isEmpty()) {
            $this->warn('No matching BagIt study folders found.');

            return self::SUCCESS;
        }

        $this->info("Found {$folders->count()} BagIt study folders to evaluate.");

        $bar = $this->output->createProgressBar($folders->count());
        $bar->setFormat('verbose');

        foreach ($folders as $folderName) {
            $this->processFolder($sourceDisk, $basePath, $folderName);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Processed', 'Skipped (has photo)', 'Skipped (study not found)', 'Skipped (no .nmrium file)', 'Skipped (no match)', 'Skipped (no image)', 'Failed'],
            [[
                $this->processed,
                $this->skippedHasPhoto,
                $this->skippedStudyNotFound,
                $this->skippedNoFile,
                $this->skippedNoMatch,
                $this->skippedNoImage,
                $this->failed,
            ]]
        );

        return self::SUCCESS;
    }

    /**
     * Backfill photos for every dataset belonging to a single study's bag.
     */
    private function processFolder(Filesystem $sourceDisk, string $basePath, string $folderName): void
    {
        $identifier = (int) substr($folderName, 1);

        $study = Study::where('identifier', $identifier)
            ->where('is_public', true)
            ->first();

        if (! $study) {
            $this->skippedStudyNotFound++;
            $this->line("  [skip] {$folderName}: no matching public study found");

            return;
        }

        $study->loadMissing(['fsObject', 'draft', 'project', 'datasets.fsObject']);

        $remoteBagDir = "{$basePath}/{$folderName}";
        $archive = BagitArchive::open($sourceDisk, $remoteBagDir);

        if ($archive === null) {
            $this->skippedNoFile++;
            $this->line("  [skip] {$folderName}: no .nmrium file found under {$remoteBagDir}");

            return;
        }

        try {
            $contents = $archive->readNmrium();

            if ($contents === null) {
                $this->skippedNoFile++;
                $this->line("  [skip] {$folderName}: failed to read .nmrium file under {$remoteBagDir}");

                return;
            }

            $decoded = json_decode($contents, true);

            if (! is_array($decoded)) {
                $this->skippedNoFile++;
                $this->line("  [skip] {$folderName}: invalid JSON in .nmrium file under {$remoteBagDir}");

                return;
            }

            // Same envelope-unwrap as nmrxiv:backfill-study-nmrium, but kept
            // purely in memory here — never persisted to studies.nmrium.
            $nmriumInfo = $decoded['nmriumState'] ?? $decoded;

            if (! isset($nmriumInfo['data']['spectra']) || ! is_array($nmriumInfo['data']['spectra'])) {
                $this->skippedNoFile++;
                $this->line("  [skip] {$folderName}: unexpected .nmrium structure (missing data.spectra)");

                return;
            }

            // Collect every dataset's photo path (freshly written or already
            // present) so the study's own study_photo_path can be kept as
            // the combined array of all its datasets' photos.
            $datasetPhotoPaths = [];
            foreach ($study->datasets as $dataset) {
                $path = $this->processDataset($study, $dataset, $nmriumInfo, $archive);
                if ($path !== null) {
                    $datasetPhotoPaths[] = $path;
                }
            }

            if (! $this->option('dry-run') && $datasetPhotoPaths !== []) {
                $this->updateStudyPhotoPath($study, $datasetPhotoPaths);
            }
        } finally {
            $archive->close();
        }
    }

    /**
     * Backfill a single dataset's photo, matching it against the bag's own
     * (freshly-read) spectra list — not the study's stored nmrium_info.
     *
     * @param  array<string, mixed>  $nmriumInfo
     * @return string|null The dataset's photo path (new or pre-existing), or null if it has none.
     */
    private function processDataset(Study $study, Dataset $dataset, array $nmriumInfo, BagitArchive $archive): ?string
    {
        if ($dataset->dataset_photo_path && ! $this->option('force')) {
            $this->skippedHasPhoto++;

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
            $this->skippedNoMatch++;

            return null;
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
            $this->skippedNoImage++;

            return null;
        }

        if ($this->option('dry-run')) {
            $this->line("  [dry-run] Would set dataset_photo_path for dataset {$dataset->identifier}");

            return null;
        }

        try {
            $path = $study->project
                ? '/projects/'.$study->project->uuid.'/'.$study->uuid.'/'.$dataset->slug.'.png'
                : '/samples/'.$study->uuid.'/'.$dataset->slug.'.png';

            Storage::disk(config('filesystems.default_public'))->put($path, $imageBytes, 'public');

            $dataset->update(['dataset_photo_path' => $path]);

            $this->processed++;

            return $path;
        } catch (Throwable $e) {
            $this->failed++;
            $this->error("  [failed] dataset {$dataset->identifier}: {$e->getMessage()}");
            Log::error("Backfill dataset photo failed for dataset {$dataset->id}: {$e->getMessage()}");

            return null;
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
