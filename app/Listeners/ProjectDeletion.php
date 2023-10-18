<?php

namespace App\Listeners;

use App\Notifications\ProjectDeletionNotification;
use Illuminate\Support\Facades\Notification;

class ProjectDeletion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        Notification::send($event->sendTo, new ProjectDeletionNotification($event->project));
    }
}
