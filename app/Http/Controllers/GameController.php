<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Models\Message;
use App\Services\MoveService;
use App\Services\CheckResultService;
use App\Http\Resources\RoomResource;
use App\Http\Requests\MessageRequest;

class GameController extends Controller
{
    private CheckResultService $checkResultService;
    private MoveService $moveService;

    public function __construct(CheckResultService $checkResultService, MoveService $moveService)
    {
        $this->checkResultService = $checkResultService;
        $this->moveService = $moveService;
    }

    public function makeMove(Request $request)
    {
        $profile = $request->user();
        $player = $profile->player;
        $roomCode = $request->input('code');
        $player->x = $request->input('new_x');
        $player->y = $request->input('new_y');
        $room = Room::with('players')->where('code', $roomCode)->first();
        if(!$room)
            {
                return response()->json(['status' => 'error',
                'message' => 'Code wasnt transmitted'], 404);
            }
        $otherPlayer = $room->players->where('id', '!=', $player->id)->first();

        $answer = $this->moveService->checkProblems($player, $otherPlayer, $room);
        if($answer != null)
            {
                return response()->json($answer, 409);
            }
        $player->save();

        $answer = $this->checkResultService->checkResultMove($player, $otherPlayer, $room, $profile);
        if($answer != null)
            {
                return response()->json($answer, 200);
            }

        $this->moveService->changeOfTurn($player, $room);
            
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

        $this->checkResultService->checkResultExit($player, $otherPlayer, $room, $profile);
        
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

    public function writeMessage(MessageRequest $request)
    {
        $profile = $request->user();
        $player = $profile->player;
        
        $message = new Message();
        $message->player_id = $player->id;
        $message->message = $request->input('message');
        $message->save();

        return response()->json(['status' => 'success',
        'message' => 'Message sent',
        'data' => new MessageResource($message)], 200);
    }

    public function checkRoom(Request $request)
    {
        $roomCode = $request->query('code');
        $room = Room::with('players.messages')->where('code', $roomCode)->first();
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