<?php

namespace App\Notifications;

use App\Mail\BagitGenerationFailed as BagitGenerationFailedMailable;
use App\Models\Study;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Throwable;

class BagitGenerationFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Study $study,
        public string $reason,
        public Throwable $exception,
        public int $attempts,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): Mailable
    {
        return (new BagitGenerationFailedMailable(
            $this->study,
            $this->reason,
            $this->exception,
            $this->attempts,
        ))->to($notifiable->email);
    }

    public function toArray($notifiable): array
    {
        return [
            'study_id' => $this->study->id,
            'study_identifier' => $this->study->identifier,
            'reason' => $this->reason,
            'attempts' => $this->attempts,
        ];
    }
}
