<?php

namespace App\Notifications;

use App\Mail\BagitGenerationSucceeded as BagitGenerationSucceededMailable;
use App\Models\Study;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class BagitGenerationSucceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Study $study,
        public ?string $archiveUrl = null,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): Mailable
    {
        return (new BagitGenerationSucceededMailable(
            $this->study,
            $this->archiveUrl,
        ))->to($notifiable->email);
    }

    public function toArray($notifiable): array
    {
        return [
            'study_id' => $this->study->id,
            'study_identifier' => $this->study->identifier,
            'archive_url' => $this->archiveUrl,
        ];
    }
}
