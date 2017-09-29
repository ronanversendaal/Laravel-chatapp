<?php

namespace App\Http\Controllers;

use App\Events\MessageSentToThread;
use App\Http\Requests\ThreadCreateRequest;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth-admin')->except(['store', 'show']);
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

        return view('chat', ['thread' => $thread, 'messages' => $thread->messages]);
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

        try{
            $thread = Thread::create($thread);

            return redirect()->route('thread.show', $thread);
        } catch (\Exception $e){
            // @todo If room already exists we should do something.
            
            return redirect('/');
        }


    }
    /**
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        try{
            $thread = Thread::findOrFail($request->input('thread'));

            $message = $thread->messages()->create([
                'message' => $request->input('message'),
                'user_id' => $user->id
            ]);

            // Broadcast the message
            broadcast(new MessageSentToThread($user, $message, $thread));

            $thread->touch();

            return ['status' => 'Message sent!'];
        } catch(Exception $e){
            return ['status' => 'Message not sent!'];
        }

    }
}
