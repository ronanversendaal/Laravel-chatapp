<?php

namespace App\Bot\Drivers;

use App\User;
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
