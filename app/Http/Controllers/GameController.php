<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Http\Resources\RoomResource;

class GameController extends Controller
{
    public function makeMove(Request $request)
    {
        $roomCode = $request->input('code');
        $player = $request->user()->player;
        $newX = $request->input('new_x');
        $newY = $request->input('new_y');

        $room = Room::with('players')->where('code', $roomCode)->first();
        if(!$room)
            {
                return response()->json(['status' => 'error',
                'message' => 'Code wasnt transmitted'], 404);
            }

        if($room->status != 'active')
            {
                return response()->json(['status' => 'error',
                'message' => 'Game is not active'], 409);  
            }

        if ($player->player_order != $room->current_turn)
            {
                return response()->json(['status' => 'error',
                'message' => 'Its not your move now'], 409); 
            }
        
        $maze = $room->maze;
        
        if ($maze[$newY][$newX] != 0)
            {
                return response()->json(['status' => 'error',
                'message' => 'Theres a wall there'], 409); 
            }

        $player->x = $newX;
        $player->y = $newY;
        $player->save();

        if($newY == $room->exit_y && $newX == $room->exit_x)
            {
                $room->winner_id = $player->player_order;
                $room->status = 'finished';
                $room->save();
                $nextTurn = null;
                return response()->json(['status' => 'success',
                'message' => 'You win!',
                'new_x' => $newX,
                'new_y' => $newY,
                'current_turn' => $nextTurn,
                'winner' => $player->player_order]);
            }
            elseif($player->player_order == 1)
                {
                    $nextTurn = 2;
                }
                else
                {
                    $nextTurn = 1;
                }
            
            $room->current_turn = $nextTurn;
            $room->save();
            return response()->json(['status' => 'success',
                'message' => 'Move made',
                'new_x' => $newX,
                'new_y' => $newY,
                'current_turn' => $nextTurn,
                'winner' => null]);
    }

    public function exitRoom(Request $request)
    {
        $roomCode = $request->input('code');
        $room = Room::with('players')->where('code', $roomCode)->first();
        if(!$room)
            {
                return response()->json(['status' => 'error',
                'message' => 'Code wasnt transmitted'], 404);
            }
        
        $player = $request->user()->player;

        if($room->status == 'active')
            {
                $room->status = 'finished';
                if($player->player_order == 1)
                    {
                        $room->winner_id = 2;
                    }
                else $room->winner_id = 1;
            }
        
        $player->delete();

        if($room->players()->count() == 0)
            {
                $room->delete();
                return response()->json(['status' => 'success',
                'message' => 'Player succesfuly exit room'], 200);
            }
        else $room->save();
        return response()->json(['status' => 'success',
        'message' => 'Player succesfuly exit room',
        'data' => new RoomResource($room)], 200);
    }

    public function cancelRoom(Request $request)
    {
        $roomCode = $request->input('code');
        $room = Room::with('players')->where('code', $roomCode)->first();
        if(!$room)
            {
                return response()->json(['status' => 'error',
                'message' => 'Code wasnt transmitted'], 404);
            }
        
        if($room->status != 'waiting')
            {
                return response()->json(['status' => 'error',
                'message' => 'Cant cancel: game has already begun'], 409);
            }
        if ($room->players->count() > 1)
            {
                return response()->json(['status' => 'error',
                'message' => 'Cant cancel: player is alredy join'], 409);
            }
        
        $room->delete();
        return response()->json(['status' => 'success',
        'message' => 'Room deleted'], 200);
    }

    public function checkRoom(Request $request)
    {
        $roomCode = $request->query('code');
        $room = Room::with('players')->where('code', $roomCode)->first();
        if(!$room)
            {
                return response()->json(['status' => 'error',
                'message' => 'Code wasnt transmitted'], 404);
            }

        return response()->json(['status' => 'success',
        'message' => 'Successful polling',
        'data' => new RoomResource($room)], 200);
    }
}