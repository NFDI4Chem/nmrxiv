<?php

namespace App\Listeners;

use App\Notifications\StudyInviteNotification;
use Illuminate\Support\Facades\Notification;

class StudyInvite
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
        Notification::send($event->$invitedUser, new StudyInviteNotification($event->$invitation));
    }
}
