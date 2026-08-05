<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;

class MoveService
{
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