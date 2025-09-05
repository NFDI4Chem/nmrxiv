<?php

namespace App\Services;

use App\Models\FileSystemObject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Handle file integrity verification using checksums.
 *
 * This service manages checksum calculation, storage, and verification
 * to ensure file integrity between upload and storage systems.
 */
class FileIntegrityService
{
    /**
     * Store checksum information for a file system object.
     */
    public function storeChecksums(
        FileSystemObject $fileSystemObject,
        array $checksums,
        int $fileSize
    ): void {
        if ($fileSystemObject->type !== 'file') {
            return; // Only process files, not directories
        }

        $updateData = [
            'file_size' => $fileSize,
            'integrity_status' => 'pending',
        ];

        // Store checksums based on provided algorithms
        if (isset($checksums['md5'])) {
            $updateData['checksum_md5'] = $checksums['md5'];
        }

        if (isset($checksums['sha256'])) {
            $updateData['checksum_sha256'] = $checksums['sha256'];
        }

        // Set primary algorithm (prefer SHA-256)
        if (isset($checksums['sha256'])) {
            $updateData['checksum_algorithm'] = 'sha256';
        } elseif (isset($checksums['md5'])) {
            $updateData['checksum_algorithm'] = 'md5';
        }

        $fileSystemObject->update($updateData);

        Log::info('Checksums stored for file', [
            'file_id' => $fileSystemObject->id,
            'file_name' => $fileSystemObject->name,
            'algorithms' => array_keys($checksums),
            'file_size' => $fileSize,
        ]);
    }

    /**
     * Verify file integrity by downloading from storage and comparing checksums.
     *
     * @throws \Exception
     */
    public function verifyFileIntegrity(FileSystemObject $fileSystemObject): bool
    {
        if ($fileSystemObject->type !== 'file') {
            throw new \InvalidArgumentException('Can only verify integrity of files, not directories');
        }

        if (! $fileSystemObject->getPrimaryChecksum()) {
            $fileSystemObject->markIntegrityFailed('No checksum available for verification');

            return false;
        }

        try {
            // Download file from storage
            $fileContent = $this->downloadFileFromStorage($fileSystemObject);

            if ($fileContent === null) {
                $fileSystemObject->markIntegrityFailed('File not found in storage');

                return false;
            }

            // Verify file size first (quick check)
            $actualSize = strlen($fileContent);
            if ($fileSystemObject->file_size && $actualSize !== $fileSystemObject->file_size) {
                $fileSystemObject->markIntegrityFailed(
                    "File size mismatch. Expected: {$fileSystemObject->file_size}, Actual: {$actualSize}"
                );

                return false;
            }

            // Calculate and verify checksums
            $verificationResult = $this->verifyChecksums($fileSystemObject, $fileContent);

            if ($verificationResult['success']) {
                $fileSystemObject->markIntegrityVerified();

                Log::info('File integrity verified successfully', [
                    'file_id' => $fileSystemObject->id,
                    'file_name' => $fileSystemObject->name,
                    'algorithm' => $fileSystemObject->checksum_algorithm,
                    'checksum' => $fileSystemObject->getPrimaryChecksum(),
                ]);

                return true;
            } else {
                $fileSystemObject->markIntegrityFailed($verificationResult['error']);

                Log::warning('File integrity verification failed', [
                    'file_id' => $fileSystemObject->id,
                    'file_name' => $fileSystemObject->name,
                    'error' => $verificationResult['error'],
                ]);

                return false;
            }

        } catch (\Exception $e) {
            $error = 'Verification failed with exception: '.$e->getMessage();
            $fileSystemObject->markIntegrityFailed($error);

            Log::error('File integrity verification exception', [
                'file_id' => $fileSystemObject->id,
                'file_name' => $fileSystemObject->name,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Download file content from storage.
     */
    public function downloadFileFromStorage(FileSystemObject $fileSystemObject): ?string
    {
        $storagePath = ltrim($fileSystemObject->path, '/');
        $disk = Storage::disk(config('filesystems.default'));

        if (! $disk->exists($storagePath)) {
            return null;
        }

        return $disk->get($storagePath);
    }

    /**
     * Verify checksums against file content.
     */
    private function verifyChecksums(FileSystemObject $fileSystemObject, string $fileContent): array
    {
        $algorithm = $fileSystemObject->checksum_algorithm;
        $expectedChecksum = $fileSystemObject->getPrimaryChecksum();

        // Calculate checksum based on algorithm
        $actualChecksum = match ($algorithm) {
            'md5' => md5($fileContent),
            'sha256' => hash('sha256', $fileContent),
            default => hash('sha256', $fileContent),
        };

        if ($actualChecksum === $expectedChecksum) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'error' => "Checksum mismatch ({$algorithm}). Expected: {$expectedChecksum}, Actual: {$actualChecksum}",
        ];
    }

    /**
     * Get files pending integrity verification.
     */
    public function getFilesPendingVerification(int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return FileSystemObject::where('type', 'file')
            ->where('integrity_status', 'pending')
            ->whereNotNull('checksum_sha256')
            ->orWhereNotNull('checksum_md5')
            ->limit($limit)
            ->get();
    }

    /**
     * Get files with failed integrity verification.
     */
    public function getFilesWithFailedIntegrity(int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return FileSystemObject::where('type', 'file')
            ->where('integrity_status', 'failed')
            ->limit($limit)
            ->get();
    }

    /**
     * Retry verification for files with failed integrity.
     */
    public function retryFailedVerifications(int $maxAttempts = 3): array
    {
        $failedFiles = FileSystemObject::where('type', 'file')
            ->where('integrity_status', 'failed')
            ->where('verification_attempts', '<', $maxAttempts)
            ->get();

        $results = [
            'total' => $failedFiles->count(),
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($failedFiles as $file) {
            try {
                if ($this->verifyFileIntegrity($file)) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'file_id' => $file->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Calculate checksum for a string content.
     */
    public static function calculateChecksum(string $content, string $algorithm = 'sha256'): string
    {
        return match ($algorithm) {
            'md5' => md5($content),
            'sha256' => hash('sha256', $content),
            'sha1' => sha1($content),
            default => hash('sha256', $content),
        };
    }

    /**
     * Get integrity statistics.
     */
    public function getIntegrityStatistics(): array
    {
        $stats = FileSystemObject::where('type', 'file')
            ->selectRaw('
                integrity_status,
                COUNT(*) as count,
                COUNT(CASE WHEN integrity_verified_at IS NOT NULL THEN 1 END) as verified_count
            ')
            ->groupBy('integrity_status')
            ->get()
            ->keyBy('integrity_status');

        return [
            'pending' => $stats->get('pending')?->count ?? 0,
            'verified' => $stats->get('verified')?->count ?? 0,
            'failed' => $stats->get('failed')?->count ?? 0,
            'skipped' => $stats->get('skipped')?->count ?? 0,
            'total_files' => FileSystemObject::where('type', 'file')->count(),
        ];
    }
}
