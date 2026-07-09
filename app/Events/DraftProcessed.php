<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Log;

class DraftProcessed implements ShouldBroadcastNow
{
    use Dispatchable;

    public $project;

    public $sendTo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($project, $sendTo)
    {
        $this->project = $project;
        $this->sendTo = $sendTo;

        Log::info('embargo_publish_trace', [
            'stage' => 'draft_processed_event_dispatched',
            'project_id' => $project->id,
            'send_to_count' => is_countable($sendTo) ? count($sendTo) : 0,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('channel-name')];
    }
}
