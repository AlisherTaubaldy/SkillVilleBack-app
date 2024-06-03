<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use App\Models\Chat;
use App\Models\GroupUser;
use App\Models\MessageResponse;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateMessageController extends Controller
{
    public function createPrivateChat(Request $request)
    {
        $chat = Chat::create([
            'type' => 'private',
        ]);

        return response()->json(['status' => 'Private Chat Created!', 'chat_id' => $chat->id]);
    }

    public function sendMessage(Request $request)
    {
        $userId = Auth::user()->id;
        $receiverId = $request->receiver_id;

        $checkChat = PrivateMessage::where(function($query) use ($userId, $receiverId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function($query) use ($userId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $userId);
        })->first();

        if(is_null($checkChat)){
            $chatController = new ChatController();
            $chat_id = $chatController->createChat("private");
        } else{
            $chat_id = $checkChat->chat_id;
        }

        $message = PrivateMessage::create([
            'chat_id' => $chat_id,
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        $messageResponse = [
            'id' => $message->id,
            'chat_id' => $message->chat_id,
            'receiver_id' => $message->receiver_id,
            'message' => $message->message,
            'sent_by_user' => true
        ];

        broadcast(new PrivateMessageSent($messageResponse))->toOthers();

        return response()->json($messageResponse);
    }
}

