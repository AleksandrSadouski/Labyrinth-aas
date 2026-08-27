<?php
namespace App\Services\PvP;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\PvP\PvPCheckResultService;
use App\Enums\RoomStatus;

use DomainException;

class PvPGameLeaveService
{
    private PvPCheckResultService $pvpCheckResultService;

    public function __construct(PvPCheckResultService $pvpCheckResultService)
    {
        $this->pvpCheckResultService = $pvpCheckResultService;
    }

    public function exit(Profile $profile, Room $room)
    {     
        $player = $profile->player;
        $otherPlayer = $room->players->where('id', '!=', $player->id)->first();

        $this->pvpCheckResultService->checkResultExit($player, $otherPlayer, $room, $profile);

        $player->delete();

        if($room->players()->count() < Room::MIN_PLAYERS)
            {
                $room->delete();
                return null;
            }

        else 
        {$room->save();
        return $room;}
    }

    public function cancel(Room $room): void
    {      
        if($room->status != RoomStatus::Waiting)
            {
                throw new DomainException('Cant cancel: game has already begun', 409);
            }
        if ($room->players->count() > Room::MIN_PLAYERS)
            {
                throw new DomainException('Cant cancel: player is alredy join', 409);
            }
        
        $room->delete();
    }
}