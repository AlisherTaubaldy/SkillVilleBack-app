<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function create(Request $request)
    {
        $roomName = $request->input('roomName');
        $roomLink = url('/room/' . $roomName);

        // Here you can add any logic to handle room creation, e.g., logging
        return view('room', ['roomName' => $roomName, 'roomLink' => $roomLink]);
    }

    public function show($roomName)
    {
        return view('room', ['roomName' => $roomName, 'roomLink' => url('/room/' . $roomName)]);
    }
}
