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
        return response()->json($this->moveService->move($request->user(), $request->input('code'), 
        $request->input('new_x'), $request->input('new_y')), 200);
    }

    public function exitRoom(Request $request)
    {
        return response()->json(['status' => 'success',
        'message' => 'Player succesfuly exit room',
        'data' => $this->gameLeaveService->exit($request->user(), $request->input('code'))], 200);
    }

    public function cancelRoom(Request $request)
    {
        $this->gameLeaveService->cancel($request->input('code'));
        return response()->json(['status' => 'success',
        'message' => 'Room deleted'], 200);
    }

    public function writeMessage(MessageRequest $request)
    {
        return response()->json(['status' => 'success',
        'message' => 'Message sent',
        'data' => $this->messageService->write($request->user(), $request->input('message'))], 200);
    }

    public function checkRoom(Request $request)
    {
        return response()->json(['status' => 'success',
        'message' => 'Successful polling',
        'data' => $this->pollingService->poll($request->query('code'))], 200);
    }
}