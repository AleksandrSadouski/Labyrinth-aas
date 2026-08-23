<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Models\Message;
use App\Services\MoveService;
use App\Services\MessageService;
use App\Services\GameLeaveService;
use App\Services\PollingService;
use App\Http\Resources\RoomResource;
use App\Http\Requests\MessageRequest;

class GameController extends Controller
{
    private MoveService $moveService;
    private MessageService $messageService;
    private GameLeaveService $gameLeaveService;
    private PollingService $pollingService;

    public function __construct(MoveService $moveService, MessageService $messageService,
    GameLeaveService $gameLeaveService, PollingService $pollingService)
    {
        $this->moveService = $moveService;
        $this->messageService = $messageService;
        $this->gameLeaveService = $gameLeaveService;
        $this->pollingService = $pollingService;
    }

    public function makeMove(Request $request)
    {
        $answer = $this->moveService->move($request->user(), $request->input('code'), 
        $request->input('new_x'), $request->input('new_y'));
        return response()->json(['status' => 'success',
        'message' => $answer['message'],
        'data' => new RoomResource($answer['room'])], 200);
    }

    public function exitRoom(Request $request)
    {
        $room = $this->gameLeaveService->exit($request->user(), $request->input('code'));
        return response()->json(['status' => 'success',
        'message' => 'Player succesfuly exit room',
        'data' => $room ? new RoomResource($room) : null], 200);
    }

    public function cancelRoom(Request $request)
    {
        $this->gameLeaveService->cancel($request->input('code'));
        return response()->json(['status' => 'success',
        'message' => 'Room deleted'], 200);
    }

    public function writeMessage(MessageRequest $request)
    {
        $message = $this->messageService->write($request->user(), $request->input('message'));
        return response()->json(['status' => 'success',
        'message' => 'Message sent',
        'data' => new MessageResource($message)], 200);
    }

    public function checkRoom(Request $request)
    {
        $room = $this->pollingService->poll($request->query('code'));
        return response()->json(['status' => 'success',
        'message' => 'Successful polling',
        'data' => new RoomResource($room)], 200);
    }
}