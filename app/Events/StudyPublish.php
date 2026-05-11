<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Log;

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

        Log::info('embargo_publish_trace', [
            'stage' => 'study_publish_event_dispatched',
            'study_ids' => collect($studies)->pluck('id')->values()->all(),
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
