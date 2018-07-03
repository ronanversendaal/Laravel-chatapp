<?php

/**
 * 
 * @author Ronan Versendaal <ronanversendaal@hotmail.com>
 * 
 */
namespace App\Events;

use App\Message;
use App\Thread;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentToThread implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $message;

    public $thread;

    /**
     * Create a new event instance.
     * 
     * @param User   $user    The current user
     * @param string $message The message to be broadcasted.
     * @param Thread $thread  The thread to be broadcasted on
     *
     * @return void
     */
    public function __construct(User $user = null, $message, Thread $thread)
    {
        $this->user = $user;
        $this->message = $message;
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
