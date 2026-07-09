<?php

namespace App\Listeners;

use App\Events\DraftProcessed as DraftProcessedEvent;
use App\Models\User;
use App\Notifications\DraftProcessedNotification;
use App\Notifications\DraftProcessedNotificationToAdmin;
use Illuminate\Support\Facades\Log;
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
    public function handle(DraftProcessedEvent $event): void
    {
        Log::info('embargo_publish_trace', [
            'stage' => 'draft_processed_listener_handle',
            'project_id' => $event->project->id,
            'recipient_count' => is_countable($event->sendTo) ? count($event->sendTo) : 0,
        ]);

        Notification::send($event->sendTo, new DraftProcessedNotification($event->project));
        Notification::send(User::role(['super-admin'])->get(), new DraftProcessedNotificationToAdmin($event->project, null));
    }
}
