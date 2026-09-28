<?php

namespace App\Console\Commands;

use App\Models\Study;
use App\Support\Bagit\BagitArchive;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BackfillStudyNmriumFromBagit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:backfill-study-nmrium
                            {--ids= : Comma-separated study folder names (e.g. S1,S100) to process}
                            {--limit= : Limit number of study folders to process}
                            {--force : Overwrite existing nmrium_info even if already present}
                            {--dry-run : Report what would happen without writing any changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse the .nmrium file from already-generated BagIt folders and backfill the study-level nmrium table';

    private int $processed = 0;

    private int $skippedHasData = 0;

    private int $skippedNotFound = 0;

    private int $skippedNoFile = 0;

    private int $failed = 0;

    /**
     * Execute the console command.
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
            ['Processed', 'Skipped (has data)', 'Skipped (study not found)', 'Skipped (no .nmrium file)', 'Failed'],
            [[$this->processed, $this->skippedHasData, $this->skippedNotFound, $this->skippedNoFile, $this->failed]]
        );

        return self::SUCCESS;
    }

    /**
     * Parse and backfill a single study's .nmrium file.
     */
    private function processFolder(Filesystem $sourceDisk, string $basePath, string $folderName): void
    {
        $identifier = (int) substr($folderName, 1);

        $study = Study::where('identifier', $identifier)
            ->where('is_public', true)
            ->first();

        if (! $study) {
            $this->skippedNotFound++;
            $this->line("  [skip] {$folderName}: no matching public study found");

            return;
        }

        $existing = $study->nmrium;
        if ($existing && ! empty($existing->nmrium_info) && ! $this->option('force')) {
            $this->skippedHasData++;

            return;
        }

        $remoteBagDir = "{$basePath}/{$folderName}";

        // The bag folder may hold a loose data/*/nmrxiv-meta/*.nmrium file
        // (a freshly-generated, not-yet-archived bag), or just a single
        // {folder}.zip (the archived, publicly-downloadable form produced by
        // nmrxiv:backfill-bagit-archives — which is how bags actually live
        // on the public bucket in practice). BagitArchive handles both.
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

            if ($this->option('dry-run')) {
                $this->line("  [dry-run] Would backfill nmrium for study {$study->identifier} from {$remoteBagDir}");

                return;
            }

            $decoded = json_decode($contents, true);

            if (! is_array($decoded) || $decoded === []) {
                throw new \RuntimeException("Invalid or empty JSON in .nmrium file under {$remoteBagDir}");
            }

            // NMRKit's spectra/parse API wraps its response as
            // {nmriumState: {data, version}, images, logs} — confirmed by
            // NMRKit as just their API envelope, not a NMRium state format
            // change. Unwrap it back to the canonical {data, version} shape
            // (matching every historically stored nmrium_info row) so we
            // have one consistent format going forward. "images"/"logs" are
            // handled separately by nmrxiv:backfill-dataset-photo, not
            // stored here.
            $nmriumInfo = $decoded['nmriumState'] ?? $decoded;

            if (! isset($nmriumInfo['data']['spectra']) || ! is_array($nmriumInfo['data']['spectra'])) {
                throw new \RuntimeException("Unexpected .nmrium structure (missing data.spectra) in {$remoteBagDir}");
            }

            $study->nmrium()->updateOrCreate([], ['nmrium_info' => $nmriumInfo]);
            $study->forceFill(['has_nmrium' => true])->save();

            $this->processed++;
        } catch (Throwable $e) {
            $this->failed++;
            $this->error("  [failed] {$folderName}: {$e->getMessage()}");
            Log::error("Backfill study NMRium failed for {$folderName}: {$e->getMessage()}");
        } finally {
            $archive->close();
        }
    }
}
