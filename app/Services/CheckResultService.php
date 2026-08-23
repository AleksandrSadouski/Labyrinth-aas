<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\UpdateStatsService;
use App\Enums\RoomStatus;

class CheckResultService
{
    private UpdateStatsService $updateStatsService;

    public function __construct(UpdateStatsService $updateStatsService)
    {
        $this->updateStatsService = $updateStatsService;
    }

    public function checkResultMove(Player $player, Player $otherPlayer, Room $room, Profile $profile): array
    {
        if($player->y == $room->exit_y && $player->x == $room->exit_x)
            {
                if($player->player_order == 2 && $room->first_finished == false)
                    {
                $this->updateStatsService->updateStats('win', $player, $otherPlayer, $room, $profile);
                return $answer = ['message' => 'You win!',
                'room' => $room];
                    }

                elseif($player->player_order == 2 && $room->first_finished == true)
                    {
                $this->updateStatsService->updateStats('draw', $player, $otherPlayer, $room, $profile);
                return $answer = ['message' => 'Draw',
                'room' => $room];
                }

                elseif($player->player_order == 1)
                    {
                        $room->first_finished = true;
                        $room->save();
                        return null;
                    }
            }
        elseif($player->player_order == 2 && $room->first_finished == true)
            {
                $this->updateStatsService->updateStats('lose', $player, $otherPlayer, $room, $profile);
                return $answer = ['message' => 'You lose!',
                'room' => $room];
            }
            else return $answer = null;
    }

    public function checkResultExit(Player $player, Player $otherPlayer, Room $room, Profile $profile): void
    {
        if($room->status == RoomStatus::Active)
            {
                $this->updateStatsService->updateStats('lose', $player, $otherPlayer, $room, $profile);
            }
    }
}