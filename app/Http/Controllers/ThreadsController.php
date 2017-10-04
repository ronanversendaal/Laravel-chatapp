<?php

namespace App\Http\Controllers;

use App\Bot\ChatBot;
use App\Events\MessageSentToThread;
use App\Http\Requests\ThreadCreateRequest;
use App\Message;
use App\Thread;
use App\User;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\DoctrineCache;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use Doctrine\Common\Cache\FilesystemCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth-admin')->except(['store', 'show', 'sendMessage']);
    }

    /**
     * Return an overview of all threads and messages.
     *
     * Like a chat index
     *
     * @return Response
     */
    public function index()
    {
        return view('threads');
    }

    public function getThreads()
    {
        return Thread::with('messages')->get();
    }

    /**
     * @param  Thread
     * @return Response
     */
    public function show(Thread $thread)
    {
        $messages = Message::with(['user'])->whereThreadId($thread->id)->get();

        return view('chat', ['thread' => $thread, 'messages' => $messages]);
    }

    /**
     * @param  ThreadCreateRequest
     * @return [type]
     */
    public function store(Request $request)
    {
        $chatroom = str_slug($request->input('emailaddress').'-'.$request->input('name').'-'.$request->input('subject'), '-');

        $thread  = array_merge(['chatroom' => $chatroom], $request->only([
            'subject', 'name', 'emailaddress'
        ]));

        try {
            $thread = Thread::create($thread);
            // If room already exists we should do something.
        } catch (\Exception $e) {
            $thread = Thread::where(['chatroom' => $thread['chatroom']])->first();
        }

        return redirect()->route('thread.show', $thread);
    }
    /**
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        try {

            $thread = Thread::findOrFail($request->input('thread'));


            $user = Auth::user();

            $message = [
                'message' => $request->input('message'),
            ];

            if($user){
                $message['user_id'] = $user->id;
            }

            $message = $thread->messages()->create($message);

            // Broadcast the message
            broadcast(new MessageSentToThread($user, $message, $thread));

            $thread->touch();


            if(!$user){

                DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

                $config = [];

                $botman = BotManFactory::create($config, new LaravelCache(), $request);
                // start listening
                $botman->hears('test', function (BotMan $bot) use ($message, $thread) {


                    $chatbot = User::find(ChatBot::USER_ID);

                    $message = [
                        'message' => 'Test yourself.',
                        'user_id' => $chatbot->id
                    ];

                    $message = $thread->messages()->create($message);

                    broadcast(new MessageSentToThread($chatbot, $message, $thread));

                    $bot->reply($message->message);
                });
                $botman->fallback(function($bot) {
                    $bot->reply('Sorry, I did not understand these commands. Here is a list of commands I understand: ...');
                });
            
                $botman->listen();

            }


            return ['status' => 'Message sent!'];
        } catch(Exception $e){
            return ['status' => 'Message not sent!'];
        }

    }
}
