<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\DraftProcessedNotificationToAdmin;
use App\Notifications\StudyPublishNotification;
use Illuminate\Support\Facades\Notification;

class StudyPublish
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
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event): void
    {
        Notification::send($event->sendTo, new StudyPublishNotification($event->studies));
        Notification::send(User::role(['super-admin'])->get(), new DraftProcessedNotificationToAdmin(null, $event->studies));
    }
}
