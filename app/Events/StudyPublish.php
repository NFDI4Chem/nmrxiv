<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class StudyPublish implements ShouldBroadcastNow
{
    use Dispatchable;

    public $studies;

    public $sendTo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($studies, $sendTo)
    {
        $this->studies = $studies;
        $this->sendTo = $sendTo;
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
