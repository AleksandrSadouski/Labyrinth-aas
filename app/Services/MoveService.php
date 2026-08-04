<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\UpdateStatsService;

class MoveService
{
    public function checkWinOrDraw(Player $player, Player $otherPlayer, Room $room, Profile $profile): array
    {
        $updateStatsService = new UpdateStatsService();
        if($player->y == $room->exit_y && $player->x == $room->exit_x)
            {
                if($player->player_order == 2 && $room->first_finished == false)
                    {
                $updateStatsService->updateStats('win', $player, $otherPlayer, $room, $profile);
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
                $updateStatsService->updateStats('draw', $player, $otherPlayer, $room, $profile);
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
                $updateStatsService->updateStats('lose', $player, $otherPlayer, $room, $profile);
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

    public function changeOfTurn(Player $player, Room $room): void
    {
        if($player->player_order == 1)
            {
                $room->current_turn = 2;
            }
            else
                {
                    $room->turn_total++;
                    $room->current_turn = 1;
                }
    }

    public function checkProblems(Player $player, Player $otherPlayer, Room $room): array
    {
        if($room->status != 'active')
            {
                return $answer = ['status' => 'error',
                'message' => 'Game is not active'];
            }

        if ($player->player_order != $room->current_turn)
            {
                return $answer = ['status' => 'error',
                'message' => 'Its not your move now']; 
            }

        if ($otherPlayer == null)
            {
                return $answer = ['status' => 'error',
                'message' => 'Not other player'];
            }
        
        if ($room->maze[$player->y][$player->x] != 0)
            {
                return $answer = ['status' => 'error',
                'message' => 'Theres a wall there']; 
            }
        
        return $answer = null;
    }
}