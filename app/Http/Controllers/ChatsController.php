<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Message;
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
    public function fetchMessages()
    {
        // ??
        return Message::with('user')->get();
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        // Broadcast the message
        broadcast(new MessageSent($user, $message))->toOthers();

        return ['status' => 'Message sent!'];
    }   
}
