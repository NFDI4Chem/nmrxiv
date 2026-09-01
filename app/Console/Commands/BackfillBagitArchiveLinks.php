<?php

namespace App\Console\Commands;

use App\Models\Study;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use ZipArchive;

class BackfillBagitArchiveLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nmrxiv:backfill-bagit-archives
                            {--ids= : Comma-separated study folder names (e.g. S1,S100) to process}
                            {--limit= : Limit number of study folders to process}
                            {--force : Regenerate the archive even if bagit_archive_link is already set}
                            {--dry-run : Report what would happen without writing any changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zip already-generated BagIt folders on storage and backfill studies.bagit_archive_link';

    private int $processed = 0;

    private int $skippedHasLink = 0;

    private int $skippedNotFound = 0;

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
            ['Processed', 'Skipped (has link)', 'Skipped (study not found)', 'Failed'],
            [[$this->processed, $this->skippedHasLink, $this->skippedNotFound, $this->failed]]
        );

        return self::SUCCESS;
    }

    /**
     * Zip and backfill a single study's BagIt folder.
     */
    private function processFolder(FilesystemAdapter $sourceDisk, string $basePath, string $folderName): void
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

        if ($study->bagit_archive_link && ! $this->option('force')) {
            $this->skippedHasLink++;

            return;
        }

        if ($this->option('dry-run')) {
            $this->line("  [dry-run] Would archive {$folderName} for study {$study->identifier}");

            return;
        }

        $remoteBagDir = "{$basePath}/{$folderName}";
        $tempDir = storage_path('app/bagit_backfill_'.uniqid());
        $zipPath = null;

        try {
            $this->downloadDirectory($sourceDisk, $remoteBagDir, $tempDir);

            if (! file_exists($tempDir.'/bagit.txt')) {
                throw new \RuntimeException("bagit.txt not found in {$remoteBagDir}, skipping invalid bag");
            }

            $zipPath = $this->zipDirectory($tempDir, $folderName);

            // Sits beside the bag directory, never inside it, so a later bag refresh cannot delete it.
            $archiveKey = "{$basePath}/{$folderName}.zip";
            $archiveContents = file_get_contents($zipPath);
            if ($archiveContents === false) {
                throw new \RuntimeException("Failed to read generated zip for {$folderName}");
            }

            if (! $sourceDisk->put($archiveKey, $archiveContents)) {
                throw new \RuntimeException("Failed to upload archive to disk for {$folderName}: {$archiveKey}");
            }

            $archiveUrl = $sourceDisk->url($archiveKey);

            $study->update([
                'bagit_archive_link' => $archiveUrl,
                'metadata_bagit_generation_status' => 'completed',
                'metadata_bagit_generation_logs' => array_merge((array) ($study->metadata_bagit_generation_logs ?: []), [
                    'backfilled_at' => now()->toIso8601String(),
                    'bagit_archive_link' => $archiveUrl,
                    'archive_path' => $archiveKey,
                ]),
            ]);

            $this->processed++;
        } catch (Throwable $e) {
            $this->failed++;
            $this->error("  [failed] {$folderName}: {$e->getMessage()}");
            Log::error("Backfill BagIt archive failed for {$folderName}: {$e->getMessage()}");
        } finally {
            if ($zipPath && file_exists($zipPath)) {
                @unlink($zipPath);
            }
            $this->removeDirectory($tempDir);
        }
    }

    /**
     * Mirror a remote storage directory into a local temp directory.
     */
    private function downloadDirectory(FilesystemAdapter $disk, string $remoteDir, string $localDir): void
    {
        if (! is_dir($localDir)) {
            mkdir($localDir, 0755, true);
        }

        foreach ($disk->allFiles($remoteDir) as $remoteFile) {
            $relative = ltrim(substr($remoteFile, strlen($remoteDir)), '/');
            $localPath = $localDir.'/'.$relative;
            $localFileDir = dirname($localPath);

            if (! is_dir($localFileDir)) {
                mkdir($localFileDir, 0755, true);
            }

            $stream = $disk->readStream($remoteFile);
            if ($stream === null) {
                continue;
            }

            file_put_contents($localPath, stream_get_contents($stream));
            fclose($stream);
        }
    }

    /**
     * Zip a local bag directory and return the local zip path.
     */
    private function zipDirectory(string $localDir, string $identifier): string
    {
        $zipPath = storage_path('app/bagit_backfill_'.$identifier.'_'.uniqid().'.zip');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Failed to create archive for {$identifier}");
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($localDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relativePath = ltrim(str_replace($localDir.'/', '', $file->getPathname()), '/');
            $zip->addFile($file->getPathname(), $relativePath);
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * Recursively remove a local directory.
     */
    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        }

        @rmdir($directory);
    }
}
