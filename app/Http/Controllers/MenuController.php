<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\MazeService;
use App\Services\CodeGeneratorService;
use App\Http\Resources\RoomResource;
use App\Http\Resources\ProfileResource;
use App\Http\Requests\JoinRoomRequest;
use App\Http\Requests\CreateRoomRequest;

class MenuController extends Controller
{
    public function exitProfile(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success',
        'message' => 'Player log out'], 200);
    }

    public function showStats(Request $request)
    {
        $profile = $request->user();
        return response()->json(['status' => 'success',
        'message' => 'Show stats',
        'data' => new ProfileResource($profile)], 200);
    }

    public function createRoom(CreateRoomRequest $request)
    {
        $profile = $request->user();

        if ($profile->player != null && $profile->player->room_id != null)
            {
                return response()->json(['status' => 'error', 
                'message' => 'Player in other room'], 409);
            }

        $player = new Player();
        $player->profile_id = $profile->id;
        $room = new Room();

        $room->size = $request->input('size');
        $room->branch_weight = $request->input('branch_weight');
        $room->hallway_weight = $request->input('hallway_weight');

        $maze = new MazeService($room->size, $room->branch_weight, $room->hallway_weight);
        $room->maze = $maze->getMaze();
        
        $codeGeneratorService = new CodeGeneratorService();
        $room->code = $codeGeneratorService->generate();

        $entry = $maze->getEntry();
        $room->entry_x = $entry[1];
        $room->entry_y = $entry[0];
        $exit = $maze->getExit();
        $room->exit_x = $exit[1];
        $room->exit_y = $exit[0];
        $room->save();

        $player->player_order = 1;
        $player->x = $room->entry_x;
        $player->y = $room->entry_y;
        $player->room_id = $room->id;
        $player->save();

        $room->load('players');
        return response()->json(['status' => 'success',
        'message' => 'Successful creation room',
        'data' => new RoomResource($room)], 200);
    }

    public function joinRoom(JoinRoomRequest $request)
    {
        $profile = $request->user();

        if ($profile->player != null && $profile->player->room_id != null)
            {
                return response()->json(['status' => 'error', 
                'message' => 'Player in other room'], 409);
            }

        $player = new Player();
        $player->profile_id = $profile->id;
        $roomCode = $request->input('code');
        $room = Room::with('players')->where('code', $roomCode)->first();

        if (!$room)
            {
                return response()->json(['status' => 'error', 
                'message' => 'Room not found'], 404);
            }
        if ($room->status != 'waiting')
            {
                return response()->json(['status' => 'error',
                'message' => 'Room has already started the game'], 409);
            }
        if ($room->players->count() >= 2)
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
        return response()->json(['status' => 'success',
        'message' => 'Successful connection',
        'data' => new RoomResource($room)], 200);
    }
}