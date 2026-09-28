<?php

namespace App\Jobs;

use App\Enums\AssignmentValidationStatus;
use App\Models\AssignmentValidation;
use App\Support\Nmr\Assignments\AssignmentValidationUnavailableException;
use App\Support\Nmr\Assignments\InvalidAssignmentSetException;
use App\Support\Nmr\Assignments\NmrkitAssignmentValidator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Runs one queued Quickcheck through NMRKit and stores the report. When the
 * nmrshiftdb2 servlet is unavailable the job backs off and retries; invalid
 * input fails immediately.
 */
class ValidateStudyAssignments implements ShouldQueue
{
    use Queueable;

    public int $tries;

    public int $timeout;

    public function __construct(public AssignmentValidation $validation)
    {
        $this->tries = max(1, (int) config('nmrxiv.assignment_validation.job_tries'));
        $this->timeout = (int) config('nmrxiv.assignment_validation.timeout') + 30;
        $this->onQueue(config('nmrxiv.assignment_validation.queue'));
    }

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        return config('nmrxiv.assignment_validation.backoff');
    }

    public function handle(NmrkitAssignmentValidator $validator): void
    {
        $validation = $this->validation->fresh();
        if ($validation === null || ! $validation->status->isPending()) {
            return;
        }

        $validation->markRunning();

        try {
            $report = $validator->validate($validation->assignmentSet());
        } catch (InvalidAssignmentSetException $exception) {
            $validation->markFailed($exception->getMessage());

            return;
        } catch (AssignmentValidationUnavailableException $exception) {
            if ($this->attempts() >= $this->tries) {
                $validation->markFailed($exception->getMessage());

                return;
            }
            $validation->update([
                'status' => AssignmentValidationStatus::Queued,
                'error' => $exception->getMessage().' Retrying automatically.',
            ]);
            $this->release($this->backoff()[$this->attempts() - 1] ?? 900);

            return;
        }

        $validation->markCompleted($report);
    }

    public function failed(?Throwable $exception): void
    {
        $this->validation->fresh()?->markFailed('The Quickcheck could not be completed. Please try again later.');
    }
}
