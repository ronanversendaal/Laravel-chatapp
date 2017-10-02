<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Message;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        return view('create');
    }

    /**
     *  Return the messages for a certain thread
     *
     * @return [type]
     */
    public function getMessagesForThread(Thread $thread)
    {
        return Message::with(['user', 'thread'])->whereThreadId($thread->id)->get();
    }
}
