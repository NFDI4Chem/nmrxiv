<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\Schemas\Bioschemas\BioschemasHelper;
use App\Models\Dataset;
use App\Models\Study;
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
    protected $description = "Backfill datasets.dataset_photo_path (and the combined studies.study_photo_path array) from the images embedded in a study's BagIt .nmrium file";

    private int $processed = 0;

    private int $skippedHasPhoto = 0;

    private int $skippedStudyNotFound = 0;

    private int $skippedNoStudyNmrium = 0;

    private int $skippedNoMatch = 0;

    private int $skippedNoImage = 0;

    private int $failed = 0;

    /**
     * Execute the console command.
     *
     * Requires the study's own nmrium row to already exist (see
     * nmrxiv:backfill-study-nmrium) — this command matches each dataset
     * against that stored nmrium_info to work out which spectrum/image
     * belongs to it.
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
            ['Processed', 'Skipped (has photo)', 'Skipped (study not found)', 'Skipped (no study nmrium)', 'Skipped (no match)', 'Skipped (no image)', 'Failed'],
            [[
                $this->processed,
                $this->skippedHasPhoto,
                $this->skippedStudyNotFound,
                $this->skippedNoStudyNmrium,
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

        $study->loadMissing(['nmrium', 'fsObject', 'draft', 'project', 'datasets.fsObject']);

        if (! $study->nmrium || empty($study->nmrium->nmrium_info)) {
            $this->skippedNoStudyNmrium++;
            $this->line("  [skip] {$folderName}: study has no nmrium_info yet (run nmrxiv:backfill-study-nmrium first)");

            return;
        }

        $remoteBagDir = "{$basePath}/{$folderName}";
        $nmriumFile = $this->findNmriumFile($sourceDisk, $remoteBagDir);

        if (! $nmriumFile) {
            $this->skippedNoImage++;
            $this->line("  [skip] {$folderName}: no .nmrium file found under {$remoteBagDir}");

            return;
        }

        $images = $this->loadImagesById($sourceDisk, $nmriumFile);

        if ($images === []) {
            $this->skippedNoImage++;
            $this->line("  [skip] {$folderName}: .nmrium file has no embedded images");

            return;
        }

        // Collect every dataset's photo path (freshly written or already
        // present) so the study's own study_photo_path can be kept as the
        // combined array of all its datasets' photos.
        $datasetPhotoPaths = [];
        foreach ($study->datasets as $dataset) {
            $path = $this->processDataset($study, $dataset, $images);
            if ($path !== null) {
                $datasetPhotoPaths[] = $path;
            }
        }

        if (! $this->option('dry-run') && $datasetPhotoPaths !== []) {
            $this->updateStudyPhotoPath($study, $datasetPhotoPaths);
        }
    }

    /**
     * Backfill a single dataset's photo, given the study's id => base64 image map.
     *
     * @param  array<string, string>  $images
     * @return string|null The dataset's photo path (new or pre-existing), or null if it has none.
     */
    private function processDataset(Study $study, Dataset $dataset, array $images): ?string
    {
        if ($dataset->dataset_photo_path && ! $this->option('force')) {
            $this->skippedHasPhoto++;

            return $dataset->dataset_photo_path;
        }

        // Wire the already-loaded (and already eager-loaded fsObject/draft)
        // study back onto the dataset so BioschemasHelper's own `$dataset->study`
        // lookup doesn't re-query it per dataset.
        $dataset->setRelation('study', $study);

        // Reuse the same spectra <-> dataset path matching already used by
        // the study-nmrium save path, so this stays in lockstep with how
        // BioschemasHelper decides ownership everywhere else.
        $matched = BioschemasHelper::collectStudySpectraMatchingDataset($dataset);

        if ($matched === []) {
            $this->skippedNoMatch++;

            return null;
        }

        $image = null;
        foreach ($matched as $spectrum) {
            $spectrumId = $spectrum['id'] ?? null;
            if (is_string($spectrumId) && isset($images[$spectrumId])) {
                $image = $images[$spectrumId];
                break;
            }
        }

        if ($image === null) {
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

            $decoded = base64_decode($image, true);
            if ($decoded === false) {
                throw new \RuntimeException("Failed to decode base64 image for dataset {$dataset->identifier}");
            }

            Storage::disk(config('filesystems.default_public'))->put($path, $decoded, 'public');

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

    /**
     * Find the single .nmrium file under data/*\/nmrxiv-meta/ inside a bag directory.
     */
    private function findNmriumFile(Filesystem $disk, string $remoteBagDir): ?string
    {
        $matches = collect($disk->allFiles($remoteBagDir))
            ->filter(fn (string $path) => str_ends_with(strtolower($path), '.nmrium')
                && str_contains($path, '/nmrxiv-meta/'))
            ->values();

        if ($matches->count() !== 1) {
            return null;
        }

        return $matches->first();
    }

    /**
     * Read the raw .nmrium file's "images" array into an [spectrumId => base64Image] map.
     *
     * @return array<string, string>
     */
    private function loadImagesById(Filesystem $disk, string $nmriumFile): array
    {
        $contents = $disk->get($nmriumFile);
        if ($contents === null) {
            return [];
        }

        $decoded = json_decode($contents, true);
        $images = $decoded['images'] ?? [];

        if (! is_array($images)) {
            return [];
        }

        $byId = [];
        foreach ($images as $entry) {
            if (is_array($entry) && isset($entry['id'], $entry['image']) && is_string($entry['id']) && is_string($entry['image'])) {
                $byId[$entry['id']] = $entry['image'];
            }
        }

        return $byId;
    }
}
