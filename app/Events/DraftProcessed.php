<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

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
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
