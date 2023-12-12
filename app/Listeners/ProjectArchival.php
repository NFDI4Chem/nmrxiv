<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\ProjectArchivalNotification;
use App\Notifications\ProjectArchivalNotificationToAdmins;
use Illuminate\Support\Facades\Notification;

class ProjectArchival
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
        Notification::send($event->sendTo, new ProjectArchivalNotification($event->project));
        Notification::send(User::role(['super-admin'])->get(), new ProjectArchivalNotificationToAdmins($event->project));
    }
}
