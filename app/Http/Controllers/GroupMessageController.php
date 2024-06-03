<?php

namespace App\Http\Controllers;

use App\Events\GroupMessageSent;
use App\Models\Chat;
use App\Models\GroupMessage;
use App\Models\GroupUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = GroupMessage::create([
            'group_id' => $request->group_id,
            'user_id' => Auth::user()->id,
            'message' => $request->message,
        ]);

        broadcast(new GroupMessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => "Nice"
        ]);
    }

    public function createGroupChat(Request $request)
    {
//        $request->validate([
//            'user_ids' => 'required|array',
//            'user_ids.*' => 'exists:users,id',
//            'user_ids' => 'min:1'
//        ]);

        $chat = Chat::create([
            'type' => 'group',
            // любые другие необходимые поля
        ]);

        // Присвоение пользователей чату
        foreach ($request->user_ids as $userId) {
            GroupUser::create([
                'group_id' => $chat->id,
                'user_id' => $userId,
            ]);
        }

        return response()->json(['status' => 'Group Chat Created!', 'chat_id' => $chat->id]);
    }

    public function fetchMessages($groupId)
    {
        $groupMessages = GroupMessage::where('group_id', $groupId)
            ->orderBy('created_at', 'asc')
            ->get(['id', 'group_id', 'user_id', 'message', 'created_at']);

        return $groupMessages;
    }
}

