<?php

namespace App\Listeners;

use App\Events\StudyPublish as StudyPublishEvent;
use App\Models\User;
use App\Notifications\DraftProcessedNotificationToAdmin;
use App\Notifications\StudyPublishNotification;
use Illuminate\Support\Facades\Log;
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
     */
    public function handle(StudyPublishEvent $event): void
    {
        Log::info('embargo_publish_trace', [
            'stage' => 'study_publish_listener_handle',
            'study_ids' => collect($event->studies)->pluck('id')->values()->all(),
            'recipient_count' => is_countable($event->sendTo) ? count($event->sendTo) : 0,
        ]);

        Notification::send($event->sendTo, new StudyPublishNotification($event->studies));
        Notification::send(User::role(['super-admin'])->get(), new DraftProcessedNotificationToAdmin(null, $event->studies));
    }
}
