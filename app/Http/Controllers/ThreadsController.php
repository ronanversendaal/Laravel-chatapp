<?php

namespace App\Http\Controllers;

use App\Bot\ChatBot;
use App\Bot\ChatBotFactory;
use App\Bot\Conversations\Inquiry;
use App\Bot\Drivers\RvChatDriver;
use App\Events\ChatAction;
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
use BotMan\BotMan\Middleware\ApiAi;
use BotMan\Drivers\Web\WebDriver;
use Doctrine\Common\Cache\FilesystemCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->chatbot_driver = DriverManager::loadDriver(RvChatDriver::class);
        $this->chatbot_config = [];
        $this->cache = new LaravelCache();

        $this->middleware('auth-admin')->only(['threads', 'getThreads']);
        
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

        if($messages->count() < 1){
            // If there is no message, initiate one.

            $chatbot = User::find(ChatBot::USER_ID);

            $botman = ChatBotFactory::create($thread, $chatbot, $this->chatbot_config, $this->cache);

            $botman->reply('Hi there! My name is Chatbot.');

        }
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

            // @todo only if user isnt a bot
            if(!$user){

                // Activate bot

                $apiAi = ApiAi::create(env('API_AI_TOKEN'))->listenForAction();

                $chatbot = User::find(ChatBot::USER_ID);

                $botman = ChatBotFactory::create($thread, $chatbot, $this->chatbot_config, $this->cache, $request);

                $botman->middleware->received($apiAi);

                $botman->hears('input.*', function (ChatBot $bot) {
                    // Retrieve API.ai information:
                    $extras = $bot->getMessage()->getExtras();
                    $bot->reply($extras['apiReply']);

                    $bot->startConversation(new Inquiry($extras));
                })
                ->middleware($apiAi);

                $botman->hears('smalltalk.*', function (ChatBot $bot) {
                    $extras = $bot->getMessage()->getExtras();
                    $bot->reply($extras['apiReply']);
                })
                ->middleware($apiAi);

                $botman->hears('fallback', function (ChatBot $bot) {
                    $extras = $bot->getMessage()->getExtras();
                    $bot->reply($extras['apiReply']);
                })
                ->middleware($apiAi);

                $botman->listen();

            }


            return ['status' => 'Message sent!'];
        } catch(Exception $e){
            return ['status' => 'Message not sent!'];
        }

    }


    public function setAction(Request $request)
    {
        $thread = Thread::find($request->get('thread_id'));

        $user = User::find($request->get('user_id'));

        // Broadcast the message
        broadcast(new ChatAction($user, $request->get('action'), $thread));
    }
}
