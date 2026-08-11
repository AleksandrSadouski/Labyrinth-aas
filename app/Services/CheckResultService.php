<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\UpdateStatsService;

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
                return $answer = ['status' => 'success',
                'message' => 'You win!',
                'new_x' => $player->x,
                'new_y' => $player->y,
                'current_turn' => $room->current_turn,
                'winner' => $room->winner_order,
                'draw' => $room->draw];
                    }

                elseif($player->player_order == 2 && $room->first_finished == true)
                    {
                $this->updateStatsService->updateStats('draw', $player, $otherPlayer, $room, $profile);
                return $answer = ['status' => 'success',
                'message' => 'Draw',
                'new_x' => $player->x,
                'new_y' => $player->y,
                'current_turn' => $room->current_turn,
                'winner' => $room->winner_order,
                'draw' => $room->draw];
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
                return $answer = ['status' => 'success',
                'message' => 'You lose!',
                'new_x' => $player->x,
                'new_y' => $player->y,
                'current_turn' => $room->current_turn,
                'winner' => $room->winner_order,
                'draw' => $room->draw];
            }
            else return $answer = null;
    }

    public function checkResultExit(Player $player, Player $otherPlayer, Room $room, Profile $profile): void
    {
        if($room->status == 'active')
            {
                $this->updateStatsService->updateStats('lose', $player, $otherPlayer, $room, $profile);
            }
    }
}