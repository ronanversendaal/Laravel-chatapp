<?php

namespace App\Http\Controllers;

use App\Events\MessageSentToThread;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth-admin');       
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
            broadcast(new MessageSentToThread($user, $message, $thread))->toOthers();

            return ['status' => 'Message sent!'];
        } catch(Exception $e){
            return ['status' => 'Message not sent!'];
        }

    }
}
