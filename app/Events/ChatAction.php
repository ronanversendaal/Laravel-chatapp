<?php

/**
 * 
 * @author Ronan Versendaal <ronanversendaal@hotmail.com>
 * 
 */

namespace App\Events;

use App\Thread;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatAction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $action;

    public $thread;

    /**
     * Create a new event instance.
     * 
     * @param User   $user   The current user
     * @param string $action The action to be broadcasted.
     * @param Thread $thread The thread to be broadcasted on
     *
     * @return void
     */
    public function __construct(User $user = null, $action, Thread $thread)
    {
        $this->user = $user;
        $this->action = $action;
        $this->thread = $thread;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('chat.'.$this->thread->chatroom);
    }
}
