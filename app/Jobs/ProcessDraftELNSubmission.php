<?php

namespace App\Jobs;

use App\Actions\Draft\DraftProcessingLogger;
use App\Http\Controllers\FileSystemController;
use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Services\FileSystemObjectService;
use App\Services\PathGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProcessDraftELNSubmission implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $draftId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        FileSystemObjectService $fileSystemService,
        PathGeneratorService $pathGenerator,
        FileSystemController $fileSystemController,
        DraftProcessingLogger $logger
    ): void {
        $draft = Draft::find($this->draftId);

        if (! $draft) {
            Log::error("Draft not found: {$this->draftId}");

            return;
        }

        try {
            $logger->log($draft, 'info', 'Starting ELN submission processing');

            // Only process Chemotion ELN
            if (strtolower($draft->eln) !== 'chemotion') {
                $logger->log($draft, 'info', "Skipping non-Chemotion ELN: {$draft->eln}");

                return;
            }

            if (! $draft->zip_url) {
                throw new \Exception('No zip_url found for draft');
            }

            $draft->update(['status' => 'PROCESSING']);

            // Download and extract files
            $extractedFiles = $this->processZipFile($draft, $pathGenerator);

            if (empty($extractedFiles)) {
                throw new \Exception('No files extracted from zip');
            }

            // Create file system objects
            $this->createFileSystemObjects($draft, $extractedFiles, $fileSystemService);

            // Process folders for instrument detection
            $this->processFolders($draft, $fileSystemController);

            $draft->update([
                'status' => 'ZIP_PROCESSED',
                'current_step' => '1',
            ]);

            $logger->log($draft, 'info', 'Successfully completed ELN processing', [
                'files_processed' => count($extractedFiles),
            ]);

        } catch (\Exception $e) {
            $logger->log($draft, 'error', 'ELN processing failed: '.$e->getMessage());
            $draft->update(['status' => 'FAILED']);
            throw $e;
        }
    }

    /**
     * Download and extract zip file.
     */
    private function processZipFile(Draft $draft, PathGeneratorService $pathGenerator): array
    {
        // Download zip file with proxy support
        $httpClient = Http::timeout(300);

        // Configure proxy if environment variables are set
        $proxyOptions = [];

        if ($httpProxy = config('http.http_proxy')) {
            $proxyOptions['http'] = $httpProxy;
        }

        if ($httpsProxy = config('http.https_proxy')) {
            $proxyOptions['https'] = $httpsProxy;
        }

        if (! empty($proxyOptions)) {
            $httpClient = $httpClient->withOptions([
                'proxy' => $proxyOptions,
            ]);
        }

        $response = $httpClient->get($draft->zip_url);

        if (! $response->successful()) {
            throw new \Exception("Failed to download zip file. HTTP status: {$response->status()}");
        }

        // Create temp paths
        $tempZipPath = tempnam(sys_get_temp_dir(), 'eln_zip_');
        $tempExtractDir = sys_get_temp_dir().'/eln_extract_'.$this->draftId.'_'.time();

        try {
            // Save and extract zip
            file_put_contents($tempZipPath, $response->body());
            mkdir($tempExtractDir, 0755, true);

            $zip = new ZipArchive;
            if ($zip->open($tempZipPath) !== true) {
                throw new \Exception('Failed to open zip file');
            }

            $zip->extractTo($tempExtractDir);
            $zip->close();

            // Move files to storage
            return $this->moveFilesToStorage($draft, $tempExtractDir, $pathGenerator);

        } finally {
            // Cleanup
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
            $this->removeDirectory($tempExtractDir);
        }
    }

    /**
     * Move files from temp to storage.
     */
    private function moveFilesToStorage(Draft $draft, string $tempDir, PathGeneratorService $pathGenerator): array
    {
        $extractedFiles = [];
        $baseDestination = $draft->external_id;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace($tempDir.DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                $storageRelativePath = $baseDestination.'/'.$relativePath;
                $storagePath = $pathGenerator->generateDraftFilePath($draft, $storageRelativePath);

                // Ensure directory exists and move file
                $storageDir = dirname($storagePath);
                if (! Storage::exists($storageDir)) {
                    Storage::makeDirectory($storageDir);
                }

                Storage::put(ltrim($storagePath, '/'), file_get_contents($file->getPathname()));

                $extractedFiles[] = [
                    'upload' => [
                        'filename' => $file->getFilename(),
                        'total' => $file->getSize(),
                    ],
                    'fullPath' => $storageRelativePath,
                    'relativePath' => $storageRelativePath,
                    'storagePath' => $storagePath,
                ];
            }
        }

        return $extractedFiles;
    }

    /**
     * Create FileSystemObjects for extracted files.
     */
    private function createFileSystemObjects(Draft $draft, array $extractedFiles, FileSystemObjectService $fileSystemService): void
    {
        foreach ($extractedFiles as $file) {
            try {
                $fileSystemService->createDraftFileSystemObject($draft, $file, '');
            } catch (\Exception $e) {
                Log::error("Failed to create FileSystemObject for {$file['upload']['filename']}: ".$e->getMessage());
            }
        }
    }

    /**
     * Process folders for instrument detection.
     */
    private function processFolders(Draft $draft, FileSystemController $fileSystemController): void
    {
        $draftFolders = FileSystemObject::with('children')
            ->where([
                ['level', 0],
                ['status', '<>', 'missing'],
                ['draft_id', $draft->id],
            ])
            ->orderBy('type')
            ->orderBy('created_at', 'DESC')
            ->get();

        if ($draftFolders->isNotEmpty()) {
            $fileSystemController->processFolder($draftFolders);
        }
    }

    /**
     * Remove directory recursively.
     */
    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
