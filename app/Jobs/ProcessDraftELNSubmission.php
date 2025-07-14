<?php

namespace App\Jobs;

use App\Models\Draft;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

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

            // Check if the draft ELN is chemotion
            if (strtolower($draft->eln) !== 'chemotion') {
                Log::info("Draft {$this->draftId} is not from chemotion ELN system: {$draft->eln}");

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

            // Create temporary file for the zip
            $tempZipPath = tempnam(sys_get_temp_dir(), 'eln_zip_');
            file_put_contents($tempZipPath, $response->body());

            // Create destination folder using external_id
            $destinationFolder = $draft->path.'/'.$draft->external_id;

            // Ensure the destination directory exists
            if (! Storage::exists($destinationFolder)) {
                Storage::makeDirectory($destinationFolder);
            }

            // Extract the zip file
            $zip = new ZipArchive;
            $result = $zip->open($tempZipPath);

            if ($result === true) {
                // Extract to the draft folder with external_id as subfolder
                $extractPath = Storage::path($destinationFolder);
                $zip->extractTo($extractPath);
                $zip->close();

                Log::info("Successfully extracted chemotion zip file for draft {$this->draftId} to {$destinationFolder}");

                // Update draft to indicate processing is complete
                $draft->update([
                    'status' => 'zip_processed',
                ]);

            } else {
                Log::error("Failed to open zip file for draft {$this->draftId}. Error code: {$result}");
            }

            // Clean up temporary file
            unlink($tempZipPath);

        } catch (\Exception $e) {
            Log::error("Error processing ELN zip file for draft {$this->draftId}: ".$e->getMessage());
            throw $e;
        }
    }
}
