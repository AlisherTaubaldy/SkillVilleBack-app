<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\GroupMessage;
use App\Models\PrivateMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function getAllChats()
    {
        $userId = Auth::user()->id;

        $latestPrivateMessages = PrivateMessage::select('private_messages.id', 'private_messages.chat_id', 'private_messages.message')
            ->join(DB::raw('(SELECT MAX(id) as id FROM private_messages WHERE sender_id = ? OR receiver_id = ? GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)) as latest_messages'), function($join) {
                $join->on('private_messages.id', '=', 'latest_messages.id');
            })
            ->setBindings([$userId, $userId])
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'chat_id' => $message->chat_id,
                    'message' => $message->message
                ];
            });

        // Последние сообщения в группах, где состоит пользователь
        $latestGroupMessages = GroupMessage::select('group_messages.id', 'group_messages.group_id as chat_id', 'group_messages.message')
            ->join(DB::raw('(SELECT MAX(group_messages.id) as id FROM group_messages JOIN group_users ON group_messages.group_id = group_users.group_id WHERE group_users.user_id = ? GROUP BY group_messages.group_id) as latest_messages'), function($join) {
                $join->on('group_messages.id', '=', 'latest_messages.id');
            })
            ->setBindings([$userId])
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'chat_id' => $message->chat_id,
                    'message' => $message->message
                ];
            });

        // Объединение результатов
        $allChats = $latestPrivateMessages->merge($latestGroupMessages);

        return response()->json($allChats);
    }

    public function createChat($type)
    {
        $newChat = Chat::create([
            'type' => $type
        ]);

        return $newChat->id;
    }

}
