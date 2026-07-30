<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RoomResource;

class GameController extends Controller
{
    public function makeMove(Request $request)
    {

    }

    public function exitRoom(Request $request)
    {

    }

    public function checkRoom(Request $request)
    {
        $roomCode = $request->query('code');
        $room = Room::with('players')->where('code', $roomCode)->first();
        if(!$room)
            {
                response()->json(['status' => 'error',
                'message' => 'Code wasnt transmitted'], 404);
            }

        return response()->json(['status' => 'success',
        'message' => 'Successful polling',
        'data' => new RoomResource($room)], 200);
    }
}