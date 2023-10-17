<?php

namespace App\Listeners;

use App\Notifications\ProjectInviteNotification;
use Illuminate\Support\Facades\Notification;

class ProjectInvite
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        Notification::send($event->$invitedUser, new ProjectInviteNotification($event->$invitation));
    }
}
