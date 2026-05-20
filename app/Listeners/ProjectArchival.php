<?php

namespace App\Listeners;

use App\Events\ProjectArchival as ProjectArchivalEvent;
use App\Models\User;
use App\Notifications\ProjectArchivalNotification;
use App\Notifications\ProjectArchivalNotificationToAdmins;
use Illuminate\Support\Facades\Log;
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
    public function handle(ProjectArchivalEvent $event): void
    {
        Log::info('embargo_publish_trace', [
            'stage' => 'project_archival_listener_handle',
            'project_id' => $event->project->id,
            'recipient_count' => is_countable($event->sendTo) ? count($event->sendTo) : 0,
        ]);

        Notification::send($event->sendTo, new ProjectArchivalNotification($event->project));
        Notification::send(User::role(['super-admin'])->get(), new ProjectArchivalNotificationToAdmins($event->project));
    }
}
