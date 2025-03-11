<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\DraftProcessedNotifyAdmins;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        if ($this->project) {
            return (new DraftProcessedNotifyAdmins($this->project, null))->to($notifiable->email);
        } elseif ($this->studies) {
            return (new DraftProcessedNotifyAdmins(null, $this->studies))->to($notifiable->email);
        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
