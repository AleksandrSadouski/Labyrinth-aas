<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\MoveService;
use App\Http\Resources\RoomResource;

class GameController extends Controller
{
    public function makeMove(Request $request)
    {
        $profile = $request->user();
        $player = $profile->player;
        $roomCode = $request->input('code');
        $player->x = $request->input('new_x');
        $player->y = $request->input('new_y');
        $moveService = new MoveService();
        $room = Room::with('players')->where('code', $roomCode)->first();
        if(!$room)
            {
                return response()->json(['status' => 'error',
                'message' => 'Code wasnt transmitted'], 404);
            }
        $otherPlayer = $room->players->where('id', '!=', $player->id)->first();

        $answer = $moveService->checkProblems($player, $otherPlayer, $room);
        if($answer != null)
            {
                return response()->json($answer, 409);
            }
        $player->save();

        $answer = $moveService->checkWinOrDraw($player, $otherPlayer, $room, $profile);
        if($answer != null)
            {
                return response()->json($answer, 200);
            }

        $moveService->changeOfTurn($player, $room);
            
        $room->save();
        return response()->json(['status' => 'success',
            'message' => 'Move made',
            'new_x' => $player->x,
            'new_y' => $player->y,
            'current_turn' => $room->current_turn,
            'winner' => null]);
    }

    public function exitRoom(Request $request)
    {
        $profile = $request->user();
        $roomCode = $request->input('code');
        $room = Room::with('players')->where('code', $roomCode)->first();
        if(!$room)
            {
                return response()->json(['status' => 'error',
                'message' => 'Code wasnt transmitted'], 404);
            }
        
        $player = $request->user()->player;
        $otherPlayer = $room->players->where('id', '!=', $player->id)->first();

        if($room->status == 'active')
            {
                $room->status = 'finished';
                $profile->game_total++;
                $profile->lose_total++;
                $profile->rating -= 15;
                $profile->save();
                if ($otherPlayer != null)
                    {
                        $otherPlayer->profile->game_total++;
                        $otherPlayer->profile->win_total++;
                        $otherPlayer->profile->rating += 15;
                        $otherPlayer->profile->save();
                    }
                
                if($player->player_order == 1)
                    {
                        $room->winner_order = 2;
                    }
                else $room->winner_order = 1;
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