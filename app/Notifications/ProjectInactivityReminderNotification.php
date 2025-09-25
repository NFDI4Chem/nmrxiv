<?php

namespace App\Notifications;

use App\Mail\ProjectInactivityReminder as ProjectInactivityReminderMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class ProjectInactivityReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  mixed  $payload  Either a Project instance or an array of projects for digest
     */
    public function __construct(private $payload)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): Mailable
    {
        return (new ProjectInactivityReminderMailable($this->payload))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
