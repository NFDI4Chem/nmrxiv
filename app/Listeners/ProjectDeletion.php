<?php

namespace App\Listeners;

use App\Events\ProjectDeletion as ProjectDeletionEvent;
use App\Notifications\ProjectDeletionNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProjectDeletion
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ProjectDeletionEvent $event): void
    {
        Log::info('embargo_publish_trace', [
            'stage' => 'project_deletion_listener_handle',
            'project_id' => $event->project->id,
            'recipient_count' => is_countable($event->sendTo) ? count($event->sendTo) : 0,
        ]);

        Notification::send($event->sendTo, new ProjectDeletionNotification($event->project));
    }
}
