<?php

namespace App\Jobs;

use App\Models\Draft;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProcessDraftELNSubmission implements ShouldQueue
{
    use Queueable;

    protected $draftId;

    /**
     * Create a new job instance.
     */
    public function __construct($draftId)
    {
        $this->draftId = $draftId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Fetch the draft
            $draft = Draft::find($this->draftId);

            if (! $draft) {
                Log::error("Draft not found: {$this->draftId}");
                return;
            }

            // Validate zip_url exists
            if (! $draft->zip_url) {
                Log::error("No zip_url found for draft {$this->draftId}");
                return;
            }

            Log::info("Processing chemotion zip file for draft {$this->draftId} from {$draft->zip_url}");

            // Download the zip file
            $response = Http::timeout(300)->get($draft->zip_url);

            if (! $response->successful()) {
                Log::error("Failed to download zip file for draft {$this->draftId}: HTTP {$response->status()}");
                return;
            }

            // Create temporary paths
            $tempZipPath = tempnam(sys_get_temp_dir(), 'eln_zip_');
            $tempExtractDir = sys_get_temp_dir() . '/eln_extract_' . uniqid();

            // Save downloaded zip to temporary file
            file_put_contents($tempZipPath, $response->body());
            Log::info("Downloaded zip file for draft {$this->draftId} to temporary location");

            // Create destination folder using external_id
            $destinationFolder = $draft->path.'/'.$draft->external_id;
            
            // Get the Ceph storage disk
            $cephDisk = Storage::disk(env('FILESYSTEM_DRIVER'));

            // Ensure the destination directory exists on Ceph
            if (! $cephDisk->exists($destinationFolder)) {
                $cephDisk->makeDirectory($destinationFolder);
                Log::info("Created destination directory on Ceph: {$destinationFolder}");
            }

            // Create temporary extraction directory
            if (! mkdir($tempExtractDir, 0755, true)) {
                throw new \Exception("Failed to create temporary extraction directory: {$tempExtractDir}");
            }

            // Extract the zip file
            $zip = new ZipArchive;
            $result = $zip->open($tempZipPath);

            if ($result === true) {
                // Extract to temporary directory
                $zip->extractTo($tempExtractDir);
                $zip->close();

                Log::info("Successfully extracted zip file for draft {$this->draftId} to temporary directory");

                // Upload extracted files to Ceph
                $uploadedFiles = $this->uploadDirectoryToCeph($tempExtractDir, $destinationFolder, $cephDisk);

                Log::info("Successfully uploaded {$uploadedFiles} files to Ceph for draft {$this->draftId}");

                // Update draft to indicate processing is complete
                $draft->update([
                    'eln_status' => 'ZIP_PROCESSED',
                    'eln_logs' => array_merge(json_decode($draft->eln_logs, true) ?: [], [
                        'time' => now()->toDateTimeString(),
                        'external_id' => $draft->external_id,
                        'message' => "ZIP file processed successfully. Uploaded {$uploadedFiles} files to Ceph storage.",
                        'destination' => $destinationFolder,
                    ]),
                ]);

            } else {
                throw new \Exception("Failed to open zip file. ZipArchive error code: {$result}");
            }

            // Clean up temporary files
            $this->cleanupTempFiles($tempZipPath, $tempExtractDir);

        } catch (\Exception $e) {
            Log::error("Error processing ELN zip file for draft {$this->draftId}: ".$e->getMessage());
            
            // Update draft with error status
            $draft = Draft::find($this->draftId);
            if ($draft) {
                $draft->update([
                    'eln_status' => 'ERROR',
                    'eln_logs' => array_merge(
                        json_decode($draft->eln_logs, true) ?: [], [
                        'error' => $e->getMessage(),
                        'time' => now()->toDateTimeString(),
                        'external_id' => $draft->external_id,
                        'trace' => $e->getTraceAsString(),
                    ]),
                ]);
            }

            // Clean up temporary files even in case of error
            if (isset($tempZipPath) && file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
            if (isset($tempExtractDir) && is_dir($tempExtractDir)) {
                $this->removeDirectory($tempExtractDir);
            }

            throw $e;
        }
    }

    /**
     * Upload a directory and its contents to Ceph storage recursively
     *
     * @param string $localPath The local directory path
     * @param string $remotePath The remote path on Ceph
     * @param \Illuminate\Contracts\Filesystem\Filesystem $disk The storage disk
     * @return int Number of files uploaded
     */
    private function uploadDirectoryToCeph(string $localPath, string $remotePath, $disk): int
    {
        $uploadedCount = 0;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($localPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relativePath = str_replace($localPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $cephPath = $remotePath . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

            if ($file->isFile()) {
                // Upload file to Ceph
                $fileContent = file_get_contents($file->getPathname());
                
                if ($disk->put($cephPath, $fileContent)) {
                    $uploadedCount++;
                    Log::debug("Uploaded file to Ceph: {$cephPath}");
                } else {
                    Log::warning("Failed to upload file to Ceph: {$cephPath}");
                }
            } elseif ($file->isDir()) {
                // Ensure directory exists on Ceph
                if (! $disk->exists($cephPath)) {
                    $disk->makeDirectory($cephPath);
                    Log::debug("Created directory on Ceph: {$cephPath}");
                }
            }
        }

        return $uploadedCount;
    }

    /**
     * Clean up temporary files and directories
     *
     * @param string $tempZipPath
     * @param string $tempExtractDir
     */
    private function cleanupTempFiles(string $tempZipPath, string $tempExtractDir): void
    {
        // Remove temporary zip file
        if (file_exists($tempZipPath)) {
            unlink($tempZipPath);
            Log::debug("Cleaned up temporary zip file: {$tempZipPath}");
        }

        // Remove temporary extraction directory
        if (is_dir($tempExtractDir)) {
            $this->removeDirectory($tempExtractDir);
            Log::debug("Cleaned up temporary extraction directory: {$tempExtractDir}");
        }
    }

    /**
     * Recursively remove a directory and its contents
     *
     * @param string $dir
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        rmdir($dir);
    }
}
