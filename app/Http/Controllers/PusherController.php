<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;

class PusherController extends Controller
{
    public function auth(Request $request)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => 'ap2',
                'useTLS' => true
            ]
        );

        $socket_id = $request->input('socket_id');
        $channel_name = $request->input('channel_name');

        // Get the auth response
        $authResponse = $pusher->authorizeChannel($channel_name, $socket_id);

        // Return the JSON response directly
        return response($authResponse, 200);
    }
}

