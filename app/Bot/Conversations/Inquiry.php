<?php namespace App\Bot\Conversations;

use App\Bot\ChatBot;
use App\Events\MessageSentToThread;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class Inquiry extends Conversation{

    public function __construct($extras)
    {
        $this->extras = $extras;        
    }

    public function greet()
    {
        $this->startAction($this->extras['apiAction']);
    }

    /**
     * [startOption description]
     * @return [type] [description]
     */
    public function startAction($action)
    {
        switch ($action) {
            case 'input.greeting':
                $this->conversate();
                break;
            case 'input.capabilities':
                $this->showCapabilities();
                break;
            default:
                $this->say('nope');
                break;
        }
    }

    public function startContactOption()
    {
        $this->say('Contact!');        
    }

    public function startProjectsOption()
    {
        $this->say('You can view all Ronan\'s projects over <a href="'.route('thread.create').'" >here</a>');        
    }

    /**
     * This will just send all messages to a natural language processor
     * @return [type] [description]
     */
    public function conversate()
    {
        $this->say('Conversate!');   
    }

    public function showCapabilities()
    {
        $this->say("
            <ul>
                <li><a href=".route('thread.create').">Navigate to projects</a></li>
                <li><a>Compose a question via e-email</a></li>
            </ul>
            \n
            Or we could just continue chatting of course! :)
        ");   
    }

    public function run()
    {
        $this->greet();  
    }
}