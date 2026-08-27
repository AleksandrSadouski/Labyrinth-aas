<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\Menu\ProfileService;
use App\Services\Menu\LeaderboardService;
use App\Services\Room\CreateRoomService;
use App\Services\Room\JoinRoomService;
use App\Http\Requests\JoinRoomRequest;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\RenameRequest;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\StatsOtherRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\LeaderboardRequest;
use App\Http\Resources\RoomResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\LeaderboardResource;

class MenuController extends Controller
{
    private ProfileService $profileService;
    private LeaderboardService $leaderboardService;
    private CreateRoomService $createRoomService;
    private JoinRoomService $joinRoomService;

    public function __construct(ProfileService $profileService, LeaderboardService $leaderboardService, 
    CreateRoomService $createRoomService, JoinRoomService $joinRoomService)
    {
        $this->profileService = $profileService;
        $this->leaderboardService = $leaderboardService;
        $this->createRoomService = $createRoomService;
        $this->joinRoomService = $joinRoomService;
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
        'data' => new ProfileResource($request->user())], 200);
    }

    public function showStatsOtherProfile(StatsOtherRequest $request)
    {
        $profile = $this->profileService->showProfile($request->query('profile_name'));
        return response()->json(['status' => 'success',
        'message' => 'Show stats',
        'data' => new ProfileResource($profile)], 200);
    }

    public function renameProfile(RenameRequest $request)
    {
        $profile = $this->profileService->rename($request->user(), $request->input('new_name'));
        return response()->json(['status' => 'success',
        'message' => 'Profile renamed',
        'data' => new ProfileResource($profile)], 200);
    }

    public function changePassword(PasswordRequest $request)
    {
        $this->profileService->changePassword($request->user(), $request->input('old_password'), $request->input('new_password'));
        return response()->json(['status' => 'success',
        'message' => 'Password changed'], 200);
    }

    public function resetProfile(DeleteRequest $request)
    {
        $profile = $this->profileService->reset($request->user(), $request->input('password'));
        return response()->json(['status' => 'success',
        'message' => 'Profile reseted',
        'data' => new ProfileResource($profile)], 200);
    }

    public function showLeaderboard(LeaderboardRequest $request)
    {
        $leaderboard = $this->leaderboardService->getLeaderboard($request->query('type_top'));
        return response()->json(['status' => 'success',
        'message' => 'Show Leaderboard',
        'data' => LeaderboardResource::collection($leaderboard)], 200);
    }

    public function createRoom(CreateRoomRequest $request)
    {
        $room = $this->createRoomService->create($request->user(), $request->input('room_type'), $request->input('size'),
        $request->input('branch_weight'), $request->input('hallway_weight'));

        return response()->json(['status' => 'success',
        'message' => 'Successful creation room',
        'data' => new RoomResource($room)], 200);
    }

    public function joinRoom(JoinRoomRequest $request)
    {
        $room = $this->joinRoomService->join($request->user(), $request->input('room_type'), 
        $request->input('code'));
        return response()->json(['status' => 'success',
        'message' => 'Successful connection',
        'data' => new RoomResource($room)], 200);
    }
}