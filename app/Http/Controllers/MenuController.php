<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\ProfileService;
use App\Services\LeaderboardService;
use App\Services\RoomService;
use App\Http\Requests\JoinRoomRequest;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\RenameRequest;
use App\Http\Requests\DeleteRequest;

class MenuController extends Controller
{
    private ProfileService $profileService;
    private LeaderboardService $leaderboardService;
    private RoomService $roomService;

    public function __construct(ProfileService $profileService, LeaderboardService $leaderboardService, RoomService $roomService)
    {
        $this->profileService = $profileService;
        $this->leaderboardService = $leaderboardService;
        $this->roomService = $roomService;
    }

    public function exitProfile(Request $request)
    {
        $this->profileService->exit($request->user());
        return response()->json(['status' => 'success',
        'message' => 'Player log out'], 200);
    }

    public function deleteProfile(DeleteRequest $request)
    {
        $this->profileService->delete($request->user(), $request->input('password'));
        return response()->json(['status' => 'success',
        'message' => 'Profile successfully deleted'], 200);  
    }

    public function showStatsProfile(Request $request)
    {
        return response()->json(['status' => 'success',
        'message' => 'Show stats',
        'data' => $this->profileService->showStats($request->user())], 200);
    }

    public function renameProfile(RenameRequest $request)
    {
        return response()->json(['status' => 'success',
        'message' => 'Profile renamed',
        'data' => $this->profileService->rename($request->user(), $request->input('new_name'))], 200);
    }

    public function showLeaderboardRating(Request $request)
    {
        return response()->json(['status' => 'success',
        'message' => 'Show Leaderboard',
        'data' => $this->leaderboardService->show('max_rating_top', 'rating', 10)], 200);
    }

    public function createRoom(CreateRoomRequest $request)
    {
        return response()->json(['status' => 'success',
        'message' => 'Successful creation room',
        'data' => $this->roomService->create($request->user(), $request->input('size'),
        $request->input('branch_weight'), $request->input('hallway_weight'))], 200);
    }

    public function joinRoom(JoinRoomRequest $request)
    {
        return response()->json(['status' => 'success',
        'message' => 'Successful connection',
        'data' => $this->roomService->join($request->user(), $request->input('code'))], 200);
    }
}