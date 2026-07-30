<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\MazeService;

class MenuController extends Controller
{
    public function exitProfile(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success',
        'message' => 'Player log out'], 200);
    }

    public function createRoom(Request $request)
    {
        $profile = $request->user();
        $player = new Player();
        $player->profile_id = $profile->id;
        $room = new Room();
        $player->room_id = $room->id;
        $room->size = $request->input('size');
        $room->branch_weight = $request->input('branch_weight');
        $room->hallway_weight = $request->input('hallway_weight');
        $maze = new MazeService($room->size, $room->branch_weight, $room->hallway_weight);
        $room->maze = $maze->getMaze();
        
        $bukvar = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $roomCode = '';
        for ($i = 0; $i < 6; $i++)
        {
        $randIndex = random_int(0, strlen($bukvar) - 1);
        $roomCode = $roomCode . $bukvar[$randIndex];
        }
        $room->code = $roomCode;

        $entry = $maze->getEntry();
        $room->entry_x = $entry[1];
        $room->entry_y = $entry[0];
        $exit = $maze->getExit();
        $room->exit_x = $exit[1];
        $room->exit_y = $exit[0];

        $player->player_order = 1;
        $player->x = $room->entry_x;
        $player->y = $room->entry_y;

        $player->save();
        $room->save();
        
        return->response()->json(['status' => 'success',
        'message' => 'Successful creation room',
        'data' => new RoomResource($room)]);
    }

    public function joinRoom(Request $request)
    {
        $profile = $request->user();
        $player = new Player();
        $player->profile_id = $profile->id;
        $player->code = $request->input('code');
        $room = Room::with('players')->where('code', $player->code)->first();
        if ($room == false)
            {
                return response()->json(['status' => 'error', 
                'message' => 'Room not found'], 404);
            }
        if ($room->status != 'waiting')
            {
                return response()->json(['status' => 'error',
                'message' => 'Room has already started the game']);
            }
        if ($room->players()->count() >= 2)
            {
                return response()->json(['status' => 'error',
                'message' => 'Room is occupied'], 409);
            }
        $player->room_id = $room->id;
        $player->player_order = 2;
        $player->x = $room->entry_x;
        $player->y = $room->entry_y;
        $room->status = 'active';
        $player->save();
        $room->save();
        return->response()->json(['status' => 'success',
        'message' => 'Successful connection',
        'data' => new RoomResource($room)]);
    }
}