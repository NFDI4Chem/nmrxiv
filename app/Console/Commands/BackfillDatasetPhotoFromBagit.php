<?php

namespace App\Console\Commands;

use App\Models\Study;
use App\Support\Bagit\DatasetPhotoBackfiller;
use Illuminate\Console\Command;
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
     * The per-study matching and photo writing lives in
     * DatasetPhotoBackfiller (which also documents why the bag's own .nmrium
     * is used rather than studies.nmrium); this command only discovers the
     * bag folders and reports the outcome.
     */
    public function handle(DatasetPhotoBackfiller $backfiller): int
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
            $this->processFolder($backfiller, $folderName);
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
     * Backfill photos for every dataset belonging to a single study's bag,
     * folding the outcome into the run's totals. Any unexpected error is
     * reported against this folder only, so one bad bag never aborts the run.
     */
    private function processFolder(DatasetPhotoBackfiller $backfiller, string $folderName): void
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

        try {
            $result = $backfiller->backfillStudy(
                $study,
                $folderName,
                (bool) $this->option('force'),
                (bool) $this->option('dry-run'),
            );
        } catch (Throwable $e) {
            $this->failed++;
            $this->error("  [failed] {$folderName}: {$e->getMessage()}");
            Log::error("Backfill dataset photo failed for study folder {$folderName}: {$e->getMessage()}");

            return;
        }

        foreach ($result->messages as $message) {
            $message['type'] === 'error'
                ? $this->error($message['text'])
                : $this->line($message['text']);
        }

        $this->processed += $result->processed;
        $this->skippedHasPhoto += $result->skippedHasPhoto;
        $this->skippedNoFile += $result->skippedNoFile;
        $this->skippedNoMatch += $result->skippedNoMatch;
        $this->skippedNoImage += $result->skippedNoImage;
        $this->failed += $result->failed;
    }
}
