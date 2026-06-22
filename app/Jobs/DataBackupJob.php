<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileAttributes;
use League\Flysystem\StorageAttributes;

class DataBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Artisan::call('backup:run', ['--only-db' => true]);

        $backupDisk = $this->backupDiskName();
        $prefix = config('backup.backup.name');
        $disk = Storage::disk($backupDisk);

        $latestFile = collect($disk->listContents($prefix, false))
            ->filter(fn (StorageAttributes $item): bool => $item instanceof FileAttributes)
            ->sortByDesc(fn (FileAttributes $file): int => $file->lastModified() ?? 0)
            ->first();

        if (! $latestFile instanceof FileAttributes) {
            Log::warning('Data backup completed but no backup archive was found on storage.', [
                'disk' => $backupDisk,
                'prefix' => $prefix,
            ]);

            return;
        }

        $path = $latestFile->path();

        $disk->setVisibility($path, 'public');

        Log::info('Latest database backup marked public.', [
            'disk' => $backupDisk,
            'path' => $path,
        ]);
    }

    /**
     * @return non-empty-string
     */
    private function backupDiskName(): string
    {
        $disks = config('backup.backup.destination.disks', ['ceph']);

        return $disks[0] ?? 'ceph';
    }
}
