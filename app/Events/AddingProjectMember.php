<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddingProjectMember
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The project instance.
     */
    public $project;

    /**
     * The user instance.
     */
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct($project, $user)
    {
        $this->project = $project;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): array
    {
        return new PrivateChannel('channel-name');
    }
}
