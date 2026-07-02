<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WhatsNewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Announcement $announcement, private array $details = []) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'kind' => 'whats_new',
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
            'release_version' => $this->details['release_version'] ?? $this->announcement->release_version,
            'release_notes' => $this->details['release_notes'] ?? $this->announcement->release_notes ?? $this->announcement->message,
            'announcement_id' => $this->announcement->id,
        ];
    }
}
