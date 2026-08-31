<?php

namespace App\Jobs;

use App\Models\Study;
use App\Models\User;
use App\Notifications\BagitGenerationFailedNotification;
use App\Notifications\BagitGenerationSucceededNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use whikloj\BagItTools\Bag;
use ZipArchive;

class ProcessMetadataExtractionBagitGenerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Number of retries for network operations.
     */
    protected int $retries;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $studyId
    ) {
        $this->tries = config('nmrxiv.spectra_parsing.job_tries', 3);
        $this->timeout = config('nmrxiv.spectra_parsing.job_timeout', 600);
        $this->retries = config('nmrxiv.spectra_parsing.retry_count', 3);
        $this->onQueue(config('nmrxiv.spectra_parsing.queue', 'metadata-extraction'));
    }

    /**
     * Determine the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return config('nmrxiv.spectra_parsing.backoff', [60, 300, 900]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $study = Study::with('datasets')->find($this->studyId);

        if (! $study) {
            throw new \RuntimeException("Study {$this->studyId} not found");
        }

        try {
            // Mark as processing
            $study->update([
                'metadata_bagit_generation_status' => 'processing',
                'metadata_bagit_generation_logs' => array_merge((array) ($study->metadata_bagit_generation_logs ?: []), [
                    'started_at' => data_get($study->metadata_bagit_generation_logs, 'started_at', now()->toIso8601String()),
                    'attempt' => $this->attempts(),
                ]),
            ]);

            Log::info("Processing metadata extraction for study {$study->id} ({$study->identifier})");

            // Process the study with BagIt structure
            $result = $this->processStudy($study);

            // Mark as completed with metadata
            $study->update([
                'metadata_bagit_generation_status' => 'completed',
                'metadata_bagit_generation_logs' => array_merge((array) ($study->metadata_bagit_generation_logs ?: []), [
                    'completed_at' => now()->toIso8601String(),
                    'storage_path' => $result['location'],
                    'image_count' => $result['imageCount'],
                ]),
            ]);

            Log::info("Successfully processed study {$study->id} ({$study->identifier}): {$result['imageCount']} images saved to {$result['location']}");

            if ($study->owner) {
                Notification::send($study->owner, new BagitGenerationSucceededNotification(
                    $study->fresh(),
                    $study->fresh()->bagit_archive_link,
                ));
            }
        } catch (Throwable $e) {
            Log::warning("Attempt {$this->attempts()} failed for study {$this->studyId}: {$e->getMessage()}");

            $study->update([
                'metadata_bagit_generation_logs' => array_merge((array) ($study->metadata_bagit_generation_logs ?: []), [
                    'last_error_at' => now()->toIso8601String(),
                    'last_error_message' => $e->getMessage(),
                    'last_error_attempt' => $this->attempts(),
                ]),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure after all retry attempts have been exhausted.
     */
    public function failed(Throwable $exception): void
    {
        Log::error("Failed to process study {$this->studyId} after {$this->attempts()} attempts: {$exception->getMessage()}");

        $study = Study::find($this->studyId);

        if (! $study) {
            return;
        }

        $study->update([
            'metadata_bagit_generation_status' => 'failed',
            'metadata_bagit_generation_logs' => array_merge((array) ($study->metadata_bagit_generation_logs ?: []), [
                'failed_at' => now()->toIso8601String(),
                'error_message' => $exception->getMessage(),
                'attempts' => $this->attempts(),
            ]),
        ]);

        $admins = User::role(['super-admin'])->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new BagitGenerationFailedNotification(
                $study,
                $exception->getMessage(),
                $exception,
                $this->attempts(),
            ));
        }
    }

    /**
     * Process a single study with BagIt structure.
     */
    protected function processStudy(Study $study): array
    {
        // Remove NMRXIV: prefix if present (e.g., "NMRXIV:S1295" -> "S1295")
        $studyIdentifier = str_replace('NMRXIV:', '', $study->identifier);
        $diskName = config('nmrxiv.spectra_parsing.storage_disk', 'local');
        $disk = Storage::disk($diskName);
        $isLocalDisk = config("filesystems.disks.{$diskName}.driver") === 'local';
        $basePath = trim(config('nmrxiv.spectra_parsing.storage_path', 'spectra_parse'), '/');
        $baseDir = "{$basePath}/{$studyIdentifier}";
        $zipPath = null;
        $archiveZipPath = null;

        // The bag is always assembled with native filesystem calls, so remote disks need a real local staging directory.
        $tempWorkRoot = $isLocalDisk ? null : storage_path('app/bagit_work_'.uniqid());
        $workBaseDir = $isLocalDisk ? $disk->path($baseDir) : "{$tempWorkRoot}/{$studyIdentifier}";
        $workDataDir = "{$workBaseDir}/data";

        try {
            // Step 1: Download ZIP file
            Log::info("Step 1/7: Downloading ZIP file for study {$study->id}");
            $zipPath = $this->downloadWithRetry($study->download_url, $this->retries);

            // Step 2: Extract ZIP to data directory
            Log::info('Step 2/7: Extracting ZIP archive...');
            $studyName = $this->extractZip($zipPath, $workDataDir);

            // Step 3: Call NMRKit API
            Log::info('Step 3/7: Calling NMRKit API...');
            $jsonData = $this->callNMRKitAPI($study->download_url, $this->retries);

            // Step 4: Fetch Bio-Schema
            Log::info('Step 4/7: Fetching bio-schema...');
            $bioSchema = null;
            try {
                $bioSchema = $this->fetchBioSchema($studyIdentifier, $this->retries);
            } catch (\Exception $e) {
                Log::warning("Bio-schema fetch failed: {$e->getMessage()}. Continuing without bio-schema...");
            }

            // Step 5: Create nmrxiv-meta structure
            Log::info('Step 5/7: Creating nmrxiv-meta structure...');
            $metaDir = "{$workDataDir}/{$studyName}/nmrxiv-meta";
            $imagesDir = "{$metaDir}/images";

            $this->ensureDirectoryPath($metaDir);

            // Clean up old images directory to prevent duplicates from previous runs
            if (is_dir($imagesDir)) {
                $oldImages = array_filter(glob($imagesDir.'/*') ?: [], 'is_file');
                foreach ($oldImages as $oldImage) {
                    @unlink($oldImage);
                }
                Log::info('Cleaned up '.count($oldImages).' old image files');
            } else {
                $this->ensureDirectoryPath($imagesDir);
            }

            // Clean up spectra data
            if (isset($jsonData['data']['spectra']) && is_array($jsonData['data']['spectra'])) {
                foreach ($jsonData['data']['spectra'] as &$spectra) {
                    unset($spectra['data']);
                    unset($spectra['meta']);
                    unset($spectra['originalData']);
                    unset($spectra['originalInfo']);
                }
                unset($spectra);
            }

            // Extract and save images as PNG files
            $imageCount = 0;
            if (isset($jsonData['images']) && is_array($jsonData['images'])) {
                foreach ($jsonData['images'] as $imageData) {
                    if (isset($imageData['id']) && isset($imageData['image'])) {
                        $imageId = $imageData['id'];
                        $base64Data = $imageData['image'];

                        // Save PNG file
                        $this->savePNGFromBase64($base64Data, "{$imagesDir}/{$imageId}.png");
                        $imageCount++;
                    }
                }
            }

            // Save S{identifier}.nmrium (full API response with base64 images intact)
            $formattedJson = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            file_put_contents("{$metaDir}/{$studyIdentifier}.nmrium", $formattedJson);

            // Save bio-schema.json
            if ($bioSchema !== null) {
                $bioSchemaJson = json_encode($bioSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                file_put_contents("{$metaDir}/bio-schema.json", $bioSchemaJson);
            }

            // Step 6: Generate BagIt manifests
            Log::info('Step 6/7: Generating BagIt manifests...');
            $this->generateBagItManifests($workBaseDir);

            $archiveZipPath = $this->createBagItArchive($workBaseDir, $studyIdentifier);
            $archiveKey = 'archive/'.$studyIdentifier.'/'.$studyIdentifier.'.zip';

            if (! $isLocalDisk) {
                // The staging directory is fresh on every run, so stale remote files must be dropped explicitly.
                // This runs before the archive upload because $baseDir can contain $archiveKey.
                $disk->deleteDirectory($baseDir);
            }

            $archiveStream = fopen($archiveZipPath, 'rb');
            if ($archiveStream === false) {
                throw new \RuntimeException("Failed to open archive for upload: {$archiveZipPath}");
            }

            $archiveUploaded = $disk->put($archiveKey, $archiveStream);
            fclose($archiveStream);

            if ($archiveUploaded === false) {
                throw new \RuntimeException("Failed to upload BagIt archive to disk: {$archiveKey}");
            }

            if (! $isLocalDisk) {
                $this->uploadDirectory($workBaseDir, $disk, $baseDir);
            }

            $archiveUrl = $disk->url($archiveKey);
            $study->update([
                'bagit_archive_link' => $archiveUrl,
                'metadata_bagit_generation_logs' => array_merge((array) ($study->metadata_bagit_generation_logs ?: []), [
                    'bagit_archive_link' => $archiveUrl,
                    'archive_path' => $archiveKey,
                ]),
            ]);

            return [
                'imageCount' => $imageCount,
                'location' => $isLocalDisk ? $workBaseDir : $baseDir,
                'archiveUrl' => $archiveUrl,
            ];
        } finally {
            // Step 7: Cleanup temporary files (always runs, even on exception)
            if ($zipPath && file_exists($zipPath)) {
                Log::info('Step 7/7: Cleaning up temporary ZIP file...');
                @unlink($zipPath);
            }

            if ($archiveZipPath && file_exists($archiveZipPath)) {
                @unlink($archiveZipPath);
            }

            if ($tempWorkRoot) {
                $this->deleteLocalDirectory($tempWorkRoot);
            }
        }
    }

    /**
     * Mirror a locally assembled directory tree onto the configured storage disk.
     */
    protected function uploadDirectory(string $localPath, Filesystem $disk, string $remotePath): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($localPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relativePath = ltrim(str_replace($localPath, '', $file->getPathname()), '/');
            $stream = fopen($file->getPathname(), 'rb');

            if ($stream === false) {
                throw new \RuntimeException("Failed to open file for upload: {$file->getPathname()}");
            }

            $uploaded = $disk->put("{$remotePath}/{$relativePath}", $stream);
            fclose($stream);

            if ($uploaded === false) {
                throw new \RuntimeException("Failed to upload bag file to disk: {$remotePath}/{$relativePath}");
            }
        }
    }

    /**
     * Recursively remove a local directory tree.
     */
    protected function deleteLocalDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }

        @rmdir($path);
    }

    /**
     * Download file with retry logic.
     */
    protected function downloadWithRetry(string $url, int $retries): string
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $retries) {
            try {
                $attempt++;
                Log::debug("Download attempt {$attempt}/{$retries}...");

                $tempPath = storage_path('app/temp_'.uniqid().'.zip');
                $timeout = config('nmrxiv.spectra_parsing.download_timeout', 300);
                $response = Http::timeout($timeout)->get($url);

                if (! $response->successful()) {
                    throw new \Exception("Download failed with status {$response->status()}");
                }

                file_put_contents($tempPath, $response->body());

                return $tempPath;
            } catch (\Exception $e) {
                $lastException = $e;
                if ($attempt < $retries) {
                    Log::warning("Download failed: {$e->getMessage()}. Retrying...");
                    sleep(2);
                }
            }
        }

        throw new \Exception("Download failed after {$retries} attempts: ".$lastException->getMessage());
    }

    /**
     * Extract ZIP file and return the study name.
     */
    protected function extractZip(string $zipPath, string $extractTo): string
    {
        $zip = new ZipArchive;

        if ($zip->open($zipPath) !== true) {
            throw new \Exception("Failed to open ZIP file: {$zipPath}");
        }

        $this->ensureDirectoryPath($extractTo);

        $entries = [];
        $studyName = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $rawName = $zip->getNameIndex($i);

            if (! is_string($rawName) || $rawName === '') {
                continue;
            }

            $normalizedName = ltrim(str_replace('\\', '/', $rawName), '/');

            if ($normalizedName === '') {
                continue;
            }

            if ($studyName === null) {
                $studyName = explode('/', rtrim($normalizedName, '/'))[0] ?: null;
            }

            $entries[] = [
                'index' => $i,
                'raw' => $rawName,
                'normalized' => $normalizedName,
            ];
        }

        if (! $studyName) {
            $zip->close();
            throw new \Exception('Could not determine study name from ZIP');
        }

        $hasRootPlaceholderFile = false;
        $hasRootChildren = false;

        foreach ($entries as $entry) {
            if ($entry['normalized'] === $studyName) {
                $hasRootPlaceholderFile = true;
            }

            if (str_starts_with($entry['normalized'], $studyName.'/')) {
                $hasRootChildren = true;
            }
        }

        foreach ($entries as $entry) {
            $entryName = $entry['normalized'];

            if (str_contains($entryName, "\0") || str_starts_with($entryName, '../') || str_contains($entryName, '/../')) {
                $zip->close();
                throw new \RuntimeException("Unsafe ZIP entry path: {$entryName}");
            }

            if ($hasRootPlaceholderFile && $hasRootChildren && $entryName === $studyName) {
                continue;
            }

            $isDirectory = $this->isDirectoryEntry($zip, $entry['index'], $entryName);
            $targetPath = rtrim($extractTo, '/').'/'.$entryName;

            if ($isDirectory) {
                $this->ensureDirectoryPath($targetPath);

                continue;
            }

            $this->ensureDirectoryPath(dirname($targetPath));

            $stream = $zip->getStream($entry['raw']);
            if ($stream === false) {
                $zip->close();
                throw new \RuntimeException("Failed to read ZIP entry stream: {$entry['raw']}");
            }

            $target = fopen($targetPath, 'wb');
            if ($target === false) {
                fclose($stream);
                $zip->close();
                throw new \RuntimeException("Failed to create extracted file: {$targetPath}");
            }

            stream_copy_to_stream($stream, $target);
            fclose($target);
            fclose($stream);
        }

        $zip->close();

        return $studyName;
    }

    protected function isDirectoryEntry(ZipArchive $zip, int $entryIndex, string $entryName): bool
    {
        if (str_ends_with($entryName, '/')) {
            return true;
        }

        $opsys = null;
        $attributes = null;

        if ($zip->getExternalAttributesIndex($entryIndex, $opsys, $attributes)) {
            $fileTypeMask = 0xF000;
            $directoryFlag = 0x4000;

            return (($attributes >> 16) & $fileTypeMask) === $directoryFlag;
        }

        return false;
    }

    protected function ensureDirectoryPath(string $path): void
    {
        if (is_file($path) && ! @unlink($path)) {
            throw new \RuntimeException("Failed to remove file blocking directory path: {$path}");
        }

        if (! is_dir($path) && ! @mkdir($path, 0755, true) && ! is_dir($path)) {
            throw new \RuntimeException("Failed to create directory: {$path}");
        }
    }

    /**
     * Call NMRKit API with retry logic.
     */
    protected function callNMRKitAPI(string $url, int $retries): array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $retries) {
            try {
                $attempt++;
                Log::debug("NMRKit API attempt {$attempt}/{$retries}...");

                $timeout = config('nmrxiv.spectra_parsing.api_timeout', 300);
                $apiUrl = config('nmrxiv.spectra_parsing.nmrkit_api_url');

                $response = Http::timeout($timeout)
                    ->post($apiUrl, [
                        'url' => $url,
                        'capture_snapshot' => true,
                        'auto_processing' => true,
                        'auto_detection' => true,
                    ]);

                if (! $response->successful()) {
                    throw new \Exception("API request failed with status {$response->status()}: {$response->body()}");
                }

                return $response->json();
            } catch (\Exception $e) {
                $lastException = $e;
                if ($attempt < $retries) {
                    Log::warning("API call failed: {$e->getMessage()}. Retrying...");
                    sleep(2);
                }
            }
        }

        throw new \Exception("API call failed after {$retries} attempts: ".$lastException->getMessage());
    }

    /**
     * Fetch bio-schema from nmrxiv.org API with retry logic.
     */
    protected function fetchBioSchema(string $studyIdentifier, int $retries): array
    {
        $attempt = 0;
        $lastException = null;
        $baseUrl = config('nmrxiv.spectra_parsing.bioschema_api_url');
        $url = "{$baseUrl}/{$studyIdentifier}";

        while ($attempt < $retries) {
            try {
                $attempt++;
                Log::debug("Bio-schema attempt {$attempt}/{$retries}...");

                $response = Http::timeout(60)->get($url);

                if (! $response->successful()) {
                    throw new \Exception("Bio-schema request failed with status {$response->status()}");
                }

                return $response->json();
            } catch (\Exception $e) {
                $lastException = $e;
                if ($attempt < $retries) {
                    Log::warning("Bio-schema fetch failed: {$e->getMessage()}. Retrying...");
                    sleep(2);
                }
            }
        }

        throw new \Exception("Bio-schema fetch failed after {$retries} attempts: ".$lastException->getMessage());
    }

    /**
     * Save PNG image from base64 data.
     */
    protected function savePNGFromBase64(string $base64Data, string $outputPath): void
    {
        // Remove data:image/png;base64, prefix if present
        $base64Data = preg_replace('/^data:image\/[a-z]+;base64,/', '', $base64Data);

        $imageData = base64_decode($base64Data);

        if ($imageData === false) {
            throw new \Exception('Failed to decode base64 image data');
        }

        file_put_contents($outputPath, $imageData);
    }

    /**
     * Generate BagIt manifests using whikloj/BagItTools library.
     */
    protected function generateBagItManifests(string $bagPath): void
    {
        try {
            // The payload is already extracted here, so adopt the directory instead of creating a new bag.
            $bag = is_dir($bagPath) ? Bag::load($bagPath) : Bag::create($bagPath);

            // Defaults are a non-extended sha512 bag, which would drop bag-info.txt/tagmanifest and rename the manifest.
            $bag->setExtended(true);
            $bag->setAlgorithm('sha256');

            if (! $bag->hasBagInfoTag('Bag-Software-Agent')) {
                $bag->addBagInfoTag('Bag-Software-Agent', 'nmrXiv/1.0');
            }

            // Writes bagit.txt, manifest-sha256.txt, bag-info.txt and tagmanifest-sha256.txt.
            $bag->update();

            Log::debug('Used BagItTools library for manifest generation');
        } catch (\Exception $e) {
            Log::warning("BagIt library failed: {$e->getMessage()}. Falling back to manual generation...");

            // Fallback: Generate manually
            $this->generateBagItManually($bagPath);
        }
    }

    /**
     * Manually generate BagIt manifests.
     */
    protected function generateBagItManually(string $bagPath): void
    {
        // Create bagit.txt
        $bagitContent = "BagIt-Version: 1.0\nTag-File-Character-Encoding: UTF-8\n";
        file_put_contents($bagPath.'/bagit.txt', $bagitContent);

        // Create manifest-sha256.txt
        $manifestLines = [];
        $dataPath = $bagPath.'/data';

        if (is_dir($dataPath)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dataPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $relativePath = str_replace($bagPath.'/', '', $file->getPathname());
                    $hash = hash_file('sha256', $file->getPathname());
                    $manifestLines[] = "{$hash}  {$relativePath}";
                }
            }
        }

        file_put_contents($bagPath.'/manifest-sha256.txt', implode("\n", $manifestLines)."\n");

        // Create bag-info.txt
        $bagInfoContent = 'Payload-Oxum: '.$this->calculatePayloadOxum($dataPath)."\n";
        $bagInfoContent .= 'Bagging-Date: '.date('Y-m-d')."\n";
        $bagInfoContent .= "Bag-Software-Agent: Laravel-Queue-ProcessStudySpectraJob/1.0\n";
        file_put_contents($bagPath.'/bag-info.txt', $bagInfoContent);

        // Create tagmanifest-sha256.txt
        $tagManifestLines = [];
        foreach (['bagit.txt', 'bag-info.txt', 'manifest-sha256.txt'] as $tagFile) {
            $tagFilePath = $bagPath.'/'.$tagFile;
            if (file_exists($tagFilePath)) {
                $hash = hash_file('sha256', $tagFilePath);
                $tagManifestLines[] = "{$hash}  {$tagFile}";
            }
        }
        file_put_contents($bagPath.'/tagmanifest-sha256.txt', implode("\n", $tagManifestLines)."\n");

        Log::debug('Manual BagIt generation complete');
    }

    /**
     * Calculate Payload-Oxum (total bytes.total files).
     */
    protected function calculatePayloadOxum(string $dataPath): string
    {
        $totalBytes = 0;
        $totalFiles = 0;

        if (is_dir($dataPath)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dataPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $totalBytes += $file->getSize();
                    $totalFiles++;
                }
            }
        }

        return "{$totalBytes}.{$totalFiles}";
    }

    /**
     * Create a ZIP archive for the generated BagIt directory and return the local path.
     */
    protected function createBagItArchive(string $bagPath, string $studyIdentifier): string
    {
        $archivePath = storage_path('app/temp_'.uniqid().'_'.$studyIdentifier.'.zip');
        $zip = new ZipArchive;

        if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Failed to create archive for study {$studyIdentifier}: {$archivePath}");
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($bagPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relativePath = ltrim(str_replace($bagPath.'/', '', $file->getPathname()), '/');
            $zip->addFile($file->getPathname(), $relativePath);
        }

        $zip->close();

        return $archivePath;
    }
}
