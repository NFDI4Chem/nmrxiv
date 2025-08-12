<?php

namespace App\Jobs;

use App\Models\FileSystemObject;
use App\Services\FileIntegrityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Asynchronously verify file integrity using checksums.
 *
 * This job downloads files from storage and verifies their integrity
 * by comparing calculated checksums with stored values.
 */
class VerifyFileIntegrityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 300; // 5 minutes

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    public function __construct(
        public FileSystemObject $fileSystemObject,
        public int $delaySeconds = 0
    ) {
        // Add delay to allow S3 upload to complete
        if ($delaySeconds > 0) {
            $this->delay(now()->addSeconds($delaySeconds));
        }
    }

    /**
     * Execute the job.
     */
    public function handle(FileIntegrityService $integrityService): void
    {
        // Only verify files, not directories
        if ($this->fileSystemObject->type !== 'file') {
            Log::info('Skipping integrity verification for non-file object', [
                'id' => $this->fileSystemObject->id,
                'type' => $this->fileSystemObject->type,
                'name' => $this->fileSystemObject->name,
            ]);

            return;
        }

        // Skip if already verified
        if ($this->fileSystemObject->isIntegrityVerified()) {
            Log::info('File integrity already verified, skipping', [
                'file_id' => $this->fileSystemObject->id,
                'file_name' => $this->fileSystemObject->name,
            ]);

            return;
        }

        // Handle files without existing checksums (calculate initial checksums)
        if (! $this->fileSystemObject->getPrimaryChecksum()) {
            Log::info('No existing checksum found, calculating initial checksums', [
                'file_id' => $this->fileSystemObject->id,
                'file_name' => $this->fileSystemObject->name,
            ]);

            try {
                // Calculate and store initial checksums
                $fileIntegrityService = app(FileIntegrityService::class);
                $result = $fileIntegrityService->verifyFileIntegrity($this->fileSystemObject, true);

                if ($result['success']) {
                    Log::info('Initial checksums calculated and stored successfully', [
                        'file_id' => $this->fileSystemObject->id,
                        'file_name' => $this->fileSystemObject->name,
                        'checksums' => $result['checksums'] ?? null,
                    ]);
                } else {
                    Log::error('Failed to calculate initial checksums', [
                        'file_id' => $this->fileSystemObject->id,
                        'file_name' => $this->fileSystemObject->name,
                        'error' => $result['error'] ?? 'Unknown error',
                    ]);
                }

                return;

            } catch (\Exception $e) {
                Log::error('Exception while calculating initial checksums', [
                    'file_id' => $this->fileSystemObject->id,
                    'file_name' => $this->fileSystemObject->name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $this->fileSystemObject->markIntegrityFailed($e->getMessage());

                return;
            }
        }

        Log::info('Starting file integrity verification', [
            'file_id' => $this->fileSystemObject->id,
            'file_name' => $this->fileSystemObject->name,
            'file_path' => $this->fileSystemObject->path,
            'checksum_algorithm' => $this->fileSystemObject->checksum_algorithm,
            'attempt' => $this->attempts(),
        ]);

        try {
            $verificationSuccess = $integrityService->verifyFileIntegrity($this->fileSystemObject);

            if ($verificationSuccess) {
                Log::info('File integrity verification completed successfully', [
                    'file_id' => $this->fileSystemObject->id,
                    'file_name' => $this->fileSystemObject->name,
                    'attempts' => $this->attempts(),
                ]);
            } else {
                Log::warning('File integrity verification failed', [
                    'file_id' => $this->fileSystemObject->id,
                    'file_name' => $this->fileSystemObject->name,
                    'error' => $this->fileSystemObject->integrity_error,
                    'attempts' => $this->attempts(),
                ]);

                // If this is the last attempt, mark as permanently failed
                if ($this->attempts() >= $this->tries) {
                    Log::error('File integrity verification failed after all retries', [
                        'file_id' => $this->fileSystemObject->id,
                        'file_name' => $this->fileSystemObject->name,
                        'final_error' => $this->fileSystemObject->integrity_error,
                        'total_attempts' => $this->attempts(),
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Exception during file integrity verification', [
                'file_id' => $this->fileSystemObject->id,
                'file_name' => $this->fileSystemObject->name,
                'exception' => $e->getMessage(),
                'attempts' => $this->attempts(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('File integrity verification job failed permanently', [
            'file_id' => $this->fileSystemObject->id,
            'file_name' => $this->fileSystemObject->name,
            'exception' => $exception->getMessage(),
            'total_attempts' => $this->attempts(),
        ]);

        // Mark the file as having failed verification
        $this->fileSystemObject->markIntegrityFailed(
            'Verification job failed after '.$this->tries.' attempts: '.$exception->getMessage()
        );
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'file_integrity',
            'file_id:'.$this->fileSystemObject->id,
            'draft_id:'.($this->fileSystemObject->draft_id ?? 'none'),
            'project_id:'.($this->fileSystemObject->project_id ?? 'none'),
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        // Exponential backoff: 60s, 120s, 240s
        return [60, 120, 240];
    }

    /**
     * Determine if the job should be retried.
     */
    public function retryUntil(): \DateTime
    {
        // Retry for up to 1 hour
        return now()->addHour();
    }
}
