<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;

class MoveService
{
    public function checkWinOrDraw(Player $player, Player $otherPlayer, Room $room, Profile $profile): array
    {
        if($player->y == $room->exit_y && $player->x == $room->exit_x)
            {
                if($player->player_order == 2 && $room->first_finished == false)
                    {
                $room->winner_order = $player->player_order;
                $room->status = 'finished';
                $room->current_turn = null;
                $room->save();
                $profile->game_total++;
                $profile->win_total++;
                $profile->rating += 15;
                $profile->save();
                $otherPlayer->profile->game_total++;
                $otherPlayer->profile->lose_total++;
                $otherPlayer->profile->rating -= 15;
                $otherPlayer->profile->save();
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
                $room->status = 'finished';
                $room->draw = true;
                $room->current_turn = null;
                $room->save();
                $profile->game_total++;
                $profile->draw_total++;
                $profile->rating += 3;
                $profile->save();
                $otherPlayer->profile->game_total++;
                $otherPlayer->profile->draw_total++;
                $otherPlayer->profile->rating += 3;
                $otherPlayer->profile->save();
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
                        return $answer = [];
                    }
            }
        elseif($player->player_order == 2 && $room->first_finished == true)
            {
                $room->winner_order = $otherPlayer->player_order;
                $room->status = 'finished';
                $room->current_turn = null;
                $room->save();
                $profile->game_total++;
                $profile->lose_total++;
                $profile->rating -= 15;
                $profile->save();
                $otherPlayer->profile->game_total++;
                $otherPlayer->profile->win_total++;
                $otherPlayer->profile->rating += 15;
                $otherPlayer->profile->save();
                return $answer = ['status' => 'success',
                'message' => 'You lose!',
                'new_x' => $player->x,
                'new_y' => $player->y,
                'current_turn' => $room->current_turn,
                'winner' => $room->winner_order,
                'draw' => $room->draw];
            }
            else return $answer = [];
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

    public function checkProblems(Player $player, Player $otherPlayer, Room $room, array $maze): array
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
        
        if ($maze[$player->y][$player->x] != 0)
            {
                return $answer = ['status' => 'error',
                'message' => 'Theres a wall there']; 
            }
        
        return $answer = [];
    }
}