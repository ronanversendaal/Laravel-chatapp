<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Message;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');       
    }

    /**
     * @return Response
     */
    public function index()
    {
        return view('chat');
    }

    /**
     * @return [type]
     */
    public function getMessages()
    {
        // ??
        return Message::with('user')->get();
    }

    /**
     *  Return the messages for a certain thread
     * 
     * @return [type]
     */
    public function getMessagesForThread(Thread $thread)
    {
        return Message::with('user')->whereThreadId($thread->id)->get();
    }
}
