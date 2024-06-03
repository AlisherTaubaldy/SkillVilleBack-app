<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\GroupMessage;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function fetchMessages($chatId)
    {
        $chat = Chat::find($chatId);

        $currentUserId = auth()->id();

        if ($chat->type == 'private') {
            $messages = PrivateMessage::where('chat_id', $chatId)
                ->get()
                ->map(function($message) use ($currentUserId) {
                    return [
                        'id' => $message->id,
                        'chat_id' => $message->chat_id,
                        'receiver_id' => $message->receiver_id,
                        'message' => $message->message,
                        'sent_by_user' => $message->sender_id == $currentUserId
                    ];
                });
        } else {
            $messages = GroupMessage::where('group_id', $chatId)
                ->get()
                ->map(function($message) use ($currentUserId) {
                    return [
                        'id' => $message->id,
                        'chat_id' => $message->group_id,
                        'receiver_id' => 0,
                        'message' => $message->message,
                        'sent_by_user' => $message->user_id == $currentUserId
                    ];
                });
        }

        return response()->json($messages);
    }
}
