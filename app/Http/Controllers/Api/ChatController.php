<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;

class ChatController extends Controller
{

    /*
    =========================
    Create Conversation
    =========================
    */

    public function createConversation(Request $request)
    {

        $conversation = Conversation::create([
            'customer_id'=>$request->customer_id,
            'tailor_id'=>$request->tailor_id
        ]);

        return response()->json([
            'message'=>'Conversation created',
            'data'=>$conversation
        ]);

    }


    /*
    =========================
    Conversations List
    =========================
    */

    public function conversations($user_id)
    {

        return response()->json(
            Conversation::where('customer_id',$user_id)
            ->orWhere('tailor_id',$user_id)
            ->get()
        );

    }


    /*
    =========================
    Messages
    =========================
    */

    public function messages($conversation_id)
    {

        return response()->json(
            Message::where('conversation_id',$conversation_id)
            ->get()
        );

    }


    /*
    =========================
    Send Message
    =========================
    */

    public function sendMessage(Request $request)
    {

        $message = Message::create([
            'conversation_id'=>$request->conversation_id,
            'sender_id'=>$request->sender_id,
            'message'=>$request->message
        ]);

        return response()->json([
            'message'=>'Message sent',
            'data'=>$message
        ]);

    }


    /*
    =========================
    Delete Message
    =========================
    */

    public function deleteMessage($id)
    {

        Message::findOrFail($id)->delete();

        return response()->json([
            'message'=>'Message deleted'
        ]);

    }

}