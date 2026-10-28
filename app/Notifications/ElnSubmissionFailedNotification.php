<?php

namespace App\Notifications;

use App\Mail\ElnSubmissionFailed as ElnSubmissionFailedMailable;
use App\Models\Draft;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class ElnSubmissionFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Draft $draft,
        private string $step,
        private ?string $error = null,
        private bool $admin = false,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): Mailable
    {
        return (new ElnSubmissionFailedMailable($this->draft, $this->step, $this->error, $this->admin))
            ->to($notifiable->email);
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
