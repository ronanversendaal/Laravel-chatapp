<?php 

/**
 * 
 * @author Ronan Versendaal <ronanversendaal@hotmail.com>
 * 
 */

namespace App\Bot;

use App\Events\MessageSentToThread;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class ChatBot extends BotMan{

    const USER_ID = 1;

    protected $thread;
    private $_cache;

    public function __construct($thread, $user, $_cache, $driver, $config, $storage)
    {
        $this->thread = $thread;
        $this->user = $user;

        parent::__construct($_cache, $driver, $config, $storage);
    }

    /**
     * Adds payload to message
     * 
     * @param string $key 
     * @param string $value 
     * 
     * @return void
     */
    public function addToMessagePayload($key, $value)
    {
        $this->getDriver()->addPayload($key, $value);
    }

    /**
     * @param int $seconds Number of seconds to wait
     * 
     * @return ChatBot
     */
    public function typesAndWaits($seconds)
    {
        $this->getDriver()->typing($this->message, $this->user);

        sleep($seconds);

        return $this;
    }

    /**
     * 
     * @return ChatBot
     */
    public function stopTyping()
    {
        $this->getDriver()->stopTyping($this->message, $this->user);

        return $this;
    }

    /**
     * Broadcasts a message from the Chatbot.
     * 
     * @param string $message              The message to be sent
     * @param array  $additionalParameters Data to be used as payload 
     *                                     for the message
     * 
     * @return void
     */
    public function reply($message, $additionalParameters = [])
    {
        $this->typesAndWaits(1.2);

        $message = [
            'message' => $message,
            'user_id' => $this->user->id
        ];

        $db_message = $this->thread->messages()->create($message);

        $message = is_string($db_message->message) ? OutgoingMessage::create($db_message->message) : $message;

        $this->sendPayload($this->getDriver()->buildServicePayload($message, $this->message, $additionalParameters));

        $this->stopTyping();

        broadcast(new MessageSentToThread($this->user, $db_message, $this->thread));

    }
}