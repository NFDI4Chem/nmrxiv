<?php

namespace App\Notifications;

use App\Models\StudyInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StudyInviteNotification extends Notification
{
    use Queueable;

    private $invitation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(StudyInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        $title = 'Invitation from study - <b>'.$this->invitation->study->name.'</b>';
        $message = 'You have been invited to join the study - '.$this->invitation->study->name.' (role: '.$this->invitation['role'].') by '.$this->invitation['invited_by'].'. Please check your mail to accept or decline the invitation.';

        return [
            'title' => $title,
            'message' => $message,
        ];
    }
}
