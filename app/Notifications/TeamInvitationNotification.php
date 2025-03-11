<?php

namespace App\Notifications;

use App\Models\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $invitation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TeamInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toArray($notifiable): array
    {
        $title = 'Invitation recieved to join <b>'.$this->invitation->team->name.'</b> team';
        $message = 'You have been invited to join the '.$this->invitation->team->name.' team (role: '.$this->invitation['role'].') by '.$this->invitation['invited_by'].'. Please check your mail to accept or decline the invitation.';

        return [
            'title' => $title,
            'message' => $message,
        ];
    }
}
