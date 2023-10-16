<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectArchival implements ShouldBroadcastNow
{
    use Dispatchable;
    public $project;
    public $sendTo;

    /**
     * Create a new event instance.
     */
    public function __construct($project, $sendTo)
    {
        $this->project = $project;
        $this->sendTo = $sendTo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
