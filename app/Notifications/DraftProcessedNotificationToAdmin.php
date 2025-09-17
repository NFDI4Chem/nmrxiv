<?php

namespace App\Notifications;

use App\Mail\DraftProcessedNotifyAdmins as DraftProcessedNotifyAdminsMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class DraftProcessedNotificationToAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    private $project;

    private $studies;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($project, $studies)
    {
        $this->project = $project;
        $this->studies = $studies;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): ?Mailable
    {
        if ($this->project) {
            return (new DraftProcessedNotifyAdminsMailable($this->project, null))->to($notifiable->email);
        } elseif ($this->studies) {
            return (new DraftProcessedNotifyAdminsMailable(null, $this->studies))->to($notifiable->email);
        } else {
            return null;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
