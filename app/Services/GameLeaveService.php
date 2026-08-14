<?php
namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\CheckResultService;
use App\Http\Resources\RoomResource;
use App\Enums\RoomStatus;

use DomainException;

class GameLeaveService
{
    private CheckResultService $checkResultService;

    public function __construct(CheckResultService $checkResultService)
    {
        $this->checkResultService = $checkResultService;
    }

    public function exit(Profile $profile, string $code)
    {
        $room = Room::with('players')->where('code', $code)->first();
        if(!$room)
            {
                throw new DomainException('Code wasnt transmitted', 404);
            }
        
        $player = $profile->player;
        $otherPlayer = $room->players->where('id', '!=', $player->id)->first();

        $this->checkResultService->checkResultExit($player, $otherPlayer, $room, $profile);
        
        $player->delete();

        if($room->players()->count() == 0)
            {
                $room->delete();
                return null;
            }

        else 
        {$room->save();
        return new RoomResource($room);}
    }

    public function cancel(string $code): void
    {
        $room = Room::with('players')->where('code', $code)->first();
        if(!$room)
            {
                throw new DomainException('Code wasnt transmitted', 404);
            }
        
        if($room->status != RoomStatus::Waiting)
            {
                throw new DomainException('Cant cancel: game has already begun', 409);
            }
        if ($room->players->count() > 1)
            {
                throw new DomainException('Cant cancel: player is alredy join', 409);
            }
        
        $room->delete();
    }
}