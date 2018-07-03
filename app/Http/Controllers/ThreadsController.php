<?php 

/**
 * A controller used in MVC Patterns, which handles the requests made 
 * to handle certain CRUD actions.
 * 
 * 
 * @author Ronan Versendaal <ronanversendaal@hotmail.com>
 * 
 */

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
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Middleware\ApiAi;
use Clarkeash\Doorman\Facades\Doorman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class ThreadsController extends Controller
{

    public function __construct()
    {
        $this->chatbot_driver = DriverManager::loadDriver(RvChatDriver::class);
        $this->chatbot_config = [];
        $this->cache = new LaravelCache();

        $this->middleware('auth-admin')->only(['index', 'threads', 'getThreads']);
    }

    /**
     * Returns an overview of all threads and messages.
     *
     * @return Response
     */
    public function index()
    {
        return view('threads');
    }

    /**
     * Returns a form where users can resume an already created chat
     * 
     * @return Response
     */
    public function resume()
    {
        return view('resume');        
    }

    /**
     * Query all threads and their related messages.
     * 
     * @return Collection
     */
    public function getThreads()
    {
        return Thread::with('messages')->get();
    }

    /**
     * Returns a view with the current requested thread and its messages. 
     * 
     * @param Thread $thread The requested thread
     * 
     * @return Response
     */
    public function show(Thread $thread)
    {

        $messages = Message::with(['user'])->whereThreadId($thread->id)->get();

        if ($messages->count() === 0) {
            // If there is no conversation; initiate one.

            $chatbot = User::find(ChatBot::USER_ID);

            $botman = ChatBotFactory::create($thread, $chatbot, $this->chatbot_config, $this->cache);

            $botman->reply('Hi there! My name is Chatbot.');
        }

        return view('chat', ['thread' => $thread, 'messages' => $messages]);
    }

    /**
     * Stores a new thread and creates a Doorman token to allow the user to return.
     * 
     * @param ThreadCreateRequest $request The current request when validated
     * 
     * @return Redirect
     */
    public function store(ThreadCreateRequest $request)
    {
        $chatroom = str_slug($request->input('emailaddress').'-'.$request->input('name').'-'.$request->input('subject'), '-');

        $thread  = array_merge(
            ['chatroom' => $chatroom], $request->only(
                [
                    'subject', 'name', 'emailaddress'
                ]
            )
        );

        try {
            $thread = Thread::create($thread);

            Doorman::generate()->for($request->get('emailaddress'))->make();
            // @todo Inform the user that they have got a code.

            return redirect()->route('thread.show', $thread);
        } catch (\Exception $e) {
            $thread = Thread::where(['chatroom' => $thread['chatroom']])->first();

            return redirect()->back();
        }

    }

    /**
     * Stores a new message for a thread and broadcasts the message.
     * Activates bot by passing through the input and responds accordingly.
     * 
     * @param Request $request The current request
     * 
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

            if ($user) {
                $message['user_id'] = $user->id;
            }

            $message = $thread->messages()->create($message);

            try{

                // Broadcast the message
                broadcast(new MessageSentToThread($user, $message, $thread));
            } catch(\Exception $e){
                //@todo properly handle errors - show some sort of error message.
                throw $e;
            }

            $thread->touch();


            if (!$user) {

                // Activate bot
                $apiAi = ApiAi::create(env('API_AI_TOKEN'))->listenForAction();
                $chatbot = User::find(ChatBot::USER_ID);

                $botman = ChatBotFactory::create($thread, $chatbot, $this->chatbot_config, $this->cache, $request);
                $botman->middleware->received($apiAi);

                $botman->hears(
                    'input.*', function (ChatBot $bot) {
                        // Retrieve API.ai information:
                        $extras = $bot->getMessage()->getExtras();
                        $bot->reply($extras['apiReply']);
                        $bot->startConversation(new Inquiry($extras));
                    }
                )->middleware($apiAi);

                $botman->hears(
                    'smalltalk.*', function (ChatBot $bot) {
                        $extras = $bot->getMessage()->getExtras();
                        $bot->reply($extras['apiReply']);
                    }
                )->middleware($apiAi);

                $botman->hears(
                    'fallback', function (ChatBot $bot) {
                        $extras = $bot->getMessage()->getExtras();
                        $bot->reply($extras['apiReply']);
                    }
                )->middleware($apiAi);

                $botman->listen();
            }
            return ['status' => 'Message sent!'];
        } catch (Exception $e) {
            return ['status' => 'Message not sent!'];
        }
    }

    /**
     * Validates the emailaddress together with the Doorman code to allow 
     * access to the previously created chat.
     * 
     * @param Request $request The current request
     * 
     * @return Redirect
     */
    public function postResume(Request $request)
    {
        try{

            $this->validate(
                $request, [
                    'emailaddress' => 'required|email',
                    'code' => 'required|doorman:emailaddress',
                ]
            );

            // Get the last thread
            $thread = Thread::where('emailaddress', $request->get('emailaddress'))->orderBy('updated_at', 'desc')->first();

            return redirect()->route('thread.show', $thread);

        } catch(ValidationException $e){
            return redirect()->back();
        } catch(\Exception $e){
            return redirect()->back();
        }
    }

    /** 
     * Prompts the chatbot to perform custom actions such as 
     * showing the user that the chatbot is typing.
     * 
     * @param Request $request The current request
     * 
     * @return void
     */
    public function setAction(Request $request)
    {
        $thread = Thread::find($request->get('thread_id'));

        $user = User::find($request->get('user_id'));

        // Broadcast the message
        broadcast(new ChatAction($user, $request->get('action'), $thread));
    }
}
