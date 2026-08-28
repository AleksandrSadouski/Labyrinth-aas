<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Models\Message;
use App\Services\Locators\GameLocator;
use App\Services\Shared\PollingService;
use App\Services\Shared\MessageService;
use App\Http\Resources\RoomResource;
use App\Http\Resources\MessageResource;
use App\Http\Requests\MessageRequest;

class GameController extends Controller
{
    private GameLocator $gameLocator;
    private PollingService $pollingService;
    private MessageService $messageService;

    public function __construct(GameLocator $gameLocator, 
    PollingService $pollingService,
    MessageService $messageService,)
    {
        $this->gameLocator = $gameLocator;
        $this->pollingService = $pollingService;
        $this->messageService = $messageService;
    }

    public function makeMove(Request $request)
    {
        $answer = $this->gameLocator->move($request->user(), $request->input('code'), 
        $request->input('new_x'), $request->input('new_y'));
        return response()->json(['status' => 'success',
        'message' => $answer['message'],
        'data' => new RoomResource($answer['room'])], 200);
    }

    public function exitRoom(Request $request)
    {
        $room = $this->gameLocator->exit($request->user(), $request->input('code'));
        return response()->json(['status' => 'success',
        'message' => 'Player succesfuly exit room',
        'data' => $room ? new RoomResource($room) : null], 200);
    }

    public function cancelRoom(Request $request)
    {
        $this->gameLocator->cancel($request->input('code'));
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

    public function toggleCodeboard(Request $request)
    {
        $room = $this->gameLocator->toggleCodeboard($request->input('code'));
        return response()->json(['status' => 'success',
        'message' => 'Room is toggled',
        'data' => new RoomResource($room)], 200);
    }

    public function checkRoom(Request $request)
    {
        $room = $this->pollingService->poll($request->query('code'));
        return response()->json(['status' => 'success',
        'message' => 'Successful polling',
        'data' => new RoomResource($room)], 200);
    }
}