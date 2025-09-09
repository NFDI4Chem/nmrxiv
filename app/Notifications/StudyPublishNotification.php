<?php

namespace App\Notifications;

use App\Mail\StudyPublish as StudyPublishMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class StudyPublishNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $studies;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($studies)
    {
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
    public function toMail($notifiable): Mailable
    {
        return (new StudyPublishMailable($this->studies))->to($notifiable->email);
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
