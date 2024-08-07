<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class StudyInvite implements ShouldBroadcastNow
{
    use Dispatchable;

    public $invitedUser;

    public $invitation;

    /**
     * Create a new event instance.
     */
    public function __construct($invitedUser, $invitation)
    {
        $this->invitedUser = $invitedUser;
        $this->invitation = $invitation;
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
