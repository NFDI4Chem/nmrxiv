<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\DraftProcessedNotification;
use App\Notifications\ProjectPublishNotificationToAdmins;
use Illuminate\Support\Facades\Notification;

class SendDraftProcessedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
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
        Notification::send($event->sendTo, new DraftProcessedNotification($event->project));
        Notification::send(User::role(['super-admin'])->get(), new ProjectPublishNotificationToAdmins($event->project));
    }
}
