<?php

namespace App\Notifications;

use App\Mail\EmbargoPublicationFailed as EmbargoPublicationFailedMailable;
use App\Models\Project;
use App\Models\Validation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class EmbargoPublicationFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Project $project,
        private string $reason,
        private ?Validation $validation = null,
        private ?string $exceptionClass = null,
        private bool $admin = false,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): Mailable
    {
        return (new EmbargoPublicationFailedMailable(
            $this->project,
            $this->reason,
            $this->validation,
            $this->exceptionClass,
            $this->admin,
        ))->to($notifiable->email);
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
