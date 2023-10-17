<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DataBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Artisan::call('backup:run --only-db');

        // Define the bucket name and prefix.
        $bucket = env('AWS_BUCKET');
        $prefix = env('APP_ENV').'/database';
        $disk = Storage::disk(env('FILESYSTEM_DRIVER'));

        // Get the contents of the bucket with the specified prefix.
        $contents = $disk->listContents($prefix, false);

        // Sort the contents by last modified time.
        if ($contents) {
            $sortedContents = collect($contents)->sortByDesc('last_modified');

            // Get the latest file.
            $latestFile = $sortedContents->first();

            // Set the visibility to public
            $disk->setVisibility($latestFile['path'], 'public');
        }

        // Print the URL of the latest file.
        echo 'Download the file from URL '.$latestFile['path'];
    }
}
