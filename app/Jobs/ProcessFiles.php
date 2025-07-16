<?php

namespace App\Jobs;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessFiles implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * The draft instance.
     */
    public Draft $draft;

    /**
     * Create a new job instance.
     */
    public function __construct(Draft $draft)
    {
        $this->draft = $draft;
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->draft->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::transaction(function () {
                $this->processFileSystemObjects();
            });
        } catch (\Exception $e) {
            Log::error('Failed to process files for draft '.$this->draft->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Process file system objects for the draft.
     */
    private function processFileSystemObjects(): void
    {
        $draftFSObjects = $this->getDraftFileSystemObjects();
        $missingFileAdded = false;

        foreach ($draftFSObjects as $fsObject) {
            if ($this->updateFileSystemObjectStatus($fsObject)) {
                $missingFileAdded = true;
            }
        }

        if ($missingFileAdded) {
            $this->handleMissingFilesRestored();
        }
    }

    /**
     * Get draft file system objects that need processing.
     */
    private function getDraftFileSystemObjects()
    {
        return FileSystemObject::with('children')
            ->where('draft_id', $this->draft->id)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'missing');
            })
            ->get();
    }

    /**
     * Update the status of a file system object.
     *
     * @return bool True if a missing file was restored, false otherwise
     */
    private function updateFileSystemObjectStatus(FileSystemObject $fsObject): bool
    {
        if (! $fsObject->path) {
            return false;
        }

        $wasMissing = $fsObject->status === 'missing';
        $exists = Storage::disk(config('filesystems.default'))->exists($fsObject->path);

        $fsObject->status = $exists ? 'present' : 'missing';
        $fsObject->save();

        // Return true if a previously missing file is now present
        return $wasMissing && $exists;
    }

    /**
     * Handle the case when missing files have been restored.
     */
    private function handleMissingFilesRestored(): void
    {
        $project = Project::where('draft_id', $this->draft->id)->first();

        if (! $project) {
            Log::warning('No project found for draft '.$this->draft->id);

            return;
        }

        // Clear download URLs for all studies
        foreach ($project->studies as $study) {
            $study->update(['download_url' => null]);
        }

        // Dispatch archive job to regenerate archives
        ArchiveStudy::dispatch($project);
    }

    /**
     * Process files in a directory and update metadata.
     * Note: This method is currently not used but kept for potential future use.
     */
    public function updateFileMetadata(string $path): void
    {
        try {
            $disk = Storage::disk(config('filesystems.default'));
            $files = $disk->allFiles($path);

            foreach ($files as $filePath) {
                $this->updateSingleFileMetadata($filePath);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update file metadata for path: '.$path, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update metadata for a single file.
     */
    private function updateSingleFileMetadata(string $filePath): void
    {
        $fsObject = FileSystemObject::where('path', '/'.$filePath)->first();

        if (! $fsObject) {
            return;
        }

        try {
            $disk = Storage::disk(config('filesystems.default'));
            $size = $disk->size($filePath);
            $lastModified = $disk->lastModified($filePath);

            $currentInfo = json_decode($fsObject->info, true) ?? [];
            $newInfo = array_merge($currentInfo, [
                'size' => $size,
                'last_modified' => $lastModified,
                'updated_at' => now()->toISOString(),
            ]);

            $fsObject->update(['info' => json_encode($newInfo)]);
        } catch (\Exception $e) {
            Log::error('Failed to update metadata for file: '.$filePath, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
