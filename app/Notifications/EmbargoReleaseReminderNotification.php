<?php

namespace App\Notifications;

use App\Mail\EmbargoReleaseReminder as EmbargoReleaseReminderMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class EmbargoReleaseReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $project;

    private $daysUntilRelease;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($project, int $daysUntilRelease)
    {
        $this->project = $project;
        $this->daysUntilRelease = $daysUntilRelease;
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
    public function toMail($notifiable): Mailable
    {
        return (new EmbargoReleaseReminderMailable($this->project, $this->daysUntilRelease))->to($notifiable->email);
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
