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
use Illuminate\Support\Facades\Storage;

class ProcessFiles implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * The draft instance.
     *
     * @var \App\Models\Draft
     */
    public $draft;

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
        $draft = $this->draft;

        DB::transaction(function () use ($draft) {
            $draftFSObjects = FileSystemObject::with('children')
                ->where([
                    ['draft_id', $draft->id],
                ])
                ->whereNull('status')
                ->orWhere('status', 'missing')
                ->get();

            $missingFileAdded = false;
            foreach ($draftFSObjects as $fsObject) {
                if ($fsObject->path) {
                    $missingFile = false;
                    if ($fsObject->status == 'missing') {
                        $missingFile = true;
                    }
                    $exists = Storage::disk(env('FILESYSTEM_DRIVER'))->exists($fsObject->path);
                    if (! $exists) {
                        $fsObject->status = 'missing';
                    } else {
                        $fsObject->status = 'present';
                        if ($missingFile) {
                            $missingFileAdded = true;
                        }
                    }
                    $fsObject->save();
                }
            }

            if ($missingFileAdded) {
                $project = Project::where('draft_id', $draft->id)->first();
                foreach ($project->studies as $study) {
                    $study->download_url = null;
                    $study->save();
                }
                ArchiveStudy::dispatch($project);
            }

            // $this->processFiles($draft->path);
        });

    }

    public function processFiles($path)
    {
        $listing = Storage::disk(env('FILESYSTEM_DRIVER'))->listContents($path, true);
        foreach ($listing as $item) {
            $path = $item->path();
            if ($item instanceof \League\Flysystem\FileAttributes) {
                $fsObject = FileSystemObject::where([
                    ['path', '/'.$path],
                ])->first();
                if ($fsObject) {
                    $metaData = $item->extraMetadata();
                    $fsInfo = json_decode($fsObject->info, true);
                    $fsInfo = array_merge($metaData, $fsInfo);
                    $fsObject->info = $fsInfo;
                    $fsObject->save();
                }
            }
        }
    }
}
