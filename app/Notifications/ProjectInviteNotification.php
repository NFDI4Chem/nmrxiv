<?php

namespace App\Notifications;

use App\Models\ProjectInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjectInviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $invitation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ProjectInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $title = 'Invitation from project - '.$this->invitation->project->name;
        $message = 'You have been invited to join the project - '.$this->invitation->project->name.' (role: '.$this->invitation['role'].') by '.$this->invitation['invited_by'].'. Please check your mail to accept or decline the invitation.';

        return [
            'title' => $title,
            'message' => $message,
        ];
    }
}