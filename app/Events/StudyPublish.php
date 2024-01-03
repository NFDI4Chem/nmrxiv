<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class StudyPublish implements ShouldBroadcastNow
{
    use Dispatchable;

    public $studies;

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
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
