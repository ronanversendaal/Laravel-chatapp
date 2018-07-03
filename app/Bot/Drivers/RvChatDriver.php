<?php

/**
 * 
 * @author Ronan Versendaal <ronanversendaal@hotmail.com>
 * 
 */

namespace App\Bot\Drivers;

use App\User;
use App\Bot\ChatBotFactory;
use App\Bot\ChatBot;
use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Web\WebDriver;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RvChatDriver extends WebDriver
{

    /**
     * Handle for the 'typing' action. Sends a request
     * 
     * @param IncomingMessage $message The message request with action payload.
     * @param User            $user    The user for which the action was initiated
     * 
     * @return void
     */
    public function typing(IncomingMessage $message, User $user)
    {

        try{

            $parameters = [
                'thread_id' => $message->getPayload()['thread'],
                'action' => 'typing',
                'user_id' => $user->id
            ];

            // Move this to an api route to prevent crsf token hassle
            $this->http->post(route('thread.actions'), [], $parameters);

        } catch (\Exception $e) {
            throw $e;
        }

    }

    /**
     * Handle for the 'stop_typing' action. Sends a request
     * 
     * @param IncomingMessage $message The message request with action payload.
     * @param User            $user    The user for which the action was initiated
     * 
     * @return void
     */
    public function stopTyping(IncomingMessage $message, User $user)
    {

        $parameters = [
            'thread_id' => $message->getPayload()['thread'],
            'action' => 'stop_typing',
            'user_id' => $user->id
        ];

        // Move this to an api route to prevent crsf token hassle
        $this->http->post(route('thread.actions'), [], $parameters);

    }

}
