<?php
namespace App\Services\SP;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\SP\SPUpdateStatsService;
use App\Enums\RoomStatus;

class SPCheckResultService
{
    private SPUpdateStatsService $spUpdateStatsService;

    public function __construct(SPUpdateStatsService $spUpdateStatsService)
    {
        $this->spUpdateStatsService = $spUpdateStatsService;
    }

    public function checkResultMove(Player $player, Room $room, Profile $profile): array
    {
        if($player->y == $room->exit_y && $player->x == $room->exit_x)
            {
                $this->spUpdateStatsService->updateStats($room, $profile);

                return $answer = ['message' => 'You win!',
                'room' => $room];
            }
            
            else return $answer = null;
    }
}