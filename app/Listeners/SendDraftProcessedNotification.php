<?php

namespace App\Listeners;

use App\Notifications\DraftProcessedNotification;
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
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $project = $event->project;
        $owner = $project->owner;
        Notification::send($owner, new DraftProcessedNotification($project));
    }
}
