<?php

namespace App\Jobs;

use App\Models\Study;
use App\Support\Nmr\DatasetSignalDetector;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DetectDatasetSignals implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $studyId)
    {
        $this->tries = config('nmrxiv.spectra_parsing.job_tries', 3);
        $this->timeout = config('nmrxiv.spectra_parsing.job_timeout', 600);
        $this->onQueue(config('nmrxiv.spectra_parsing.queue', 'metadata-extraction'));
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return (string) $this->studyId;
    }

    /**
     * Bound the unique lock so a crashed worker cannot block re-dispatch.
     */
    public function uniqueFor(): int
    {
        return 600;
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
    public function handle(DatasetSignalDetector $detector): void
    {
        $study = Study::find($this->studyId);
        if (! $study || ! $study->is_public || blank($study->download_url)) {
            return;
        }

        $updated = $detector->detect($study);

        Log::info('DetectDatasetSignals: stored auto-detected signals', [
            'study_id' => $study->id,
            'datasets_updated' => $updated,
        ]);
    }
}
