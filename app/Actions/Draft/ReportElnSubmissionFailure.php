<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\User;
use App\Notifications\ElnSubmissionFailedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Tells the submitter and the nmrXiv admins that an ELN submission failed.
 */
class ReportElnSubmissionFailure
{
    /**
     * Never throws: a mail problem must not hide the original failure.
     */
    public function handle(Draft $draft, string $step, ?Throwable $exception = null): void
    {
        if (blank($draft->eln)) {
            return;
        }

        try {
            $submitter = $draft->owner;

            if ($submitter) {
                Notification::send($submitter, new ElnSubmissionFailedNotification($draft, $step));
            }

            $admins = User::role(['super-admin'])
                ->when($submitter, fn ($query) => $query->whereKeyNot($submitter->getKey()))
                ->get();

            Notification::send($admins, new ElnSubmissionFailedNotification($draft, $step, $exception?->getMessage(), admin: true));
        } catch (Throwable $e) {
            Log::warning('Failed to notify about an ELN submission failure', [
                'draft_id' => $draft->id,
                'step' => $step,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
