<?php

namespace App\Jobs;

use App\Models\Study;
use App\Support\Bagit\DatasetPhotoBackfiller;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Sets each dataset's photo (and the study's combined photo list) from the
 * spectra snapshots in the study's freshly generated BagIt archive. Runs after
 * ProcessMetadataExtractionBagitGenerationJob, as a job of its own so that a
 * photo problem never marks the bag itself as failed or re-runs the bag build.
 */
class GenerateDatasetPhotosFromBagitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 300;

    /**
     * Delete the job if its models no longer exist.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * @param  bool  $force  Overwrite photos that are already set. On by default: a rebuilt bag assigns new spectrum ids, so any earlier photo is stale.
     */
    public function __construct(
        public int $studyId,
        public bool $force = true,
    ) {
        $this->tries = config('nmrxiv.spectra_parsing.job_tries', 3);
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
    public function handle(DatasetPhotoBackfiller $backfiller): void
    {
        $study = Study::find($this->studyId);

        if (! $study) {
            return;
        }

        // Dataset photos are a public-facing preview, same as the backfill command.
        if (! $study->is_public) {
            Log::info("Skipping dataset photos for study {$study->id}: study is not public");

            return;
        }

        $result = $backfiller->backfillStudy($study, force: $this->force);

        foreach ($result->messages as $message) {
            Log::info('Dataset photos for study '.$study->id.': '.trim($message['text']));
        }

        Log::info("Dataset photos for study {$study->id} ({$study->identifier}): {$result->processed} written, {$result->skippedNoMatch} without a matching spectrum, {$result->skippedNoImage} without a snapshot image, {$result->failed} failed");
    }

    /**
     * Handle a job failure after all retry attempts have been exhausted.
     */
    public function failed(Throwable $exception): void
    {
        Log::error("Failed to generate dataset photos for study {$this->studyId}: {$exception->getMessage()}");
    }
}
