<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;

class GameService
{
    public function checkWinOrDraw(int $newX, int $newY, Player $player, Player $otherPlayer, Room $room, Profile $profile): array
    {
        if($newY == $room->exit_y && $newX == $room->exit_x)
            {
                if($player->player_order == 2 && $room->first_finished == false)
                    {
                $room->winner_order = $player->player_order;
                $room->status = 'finished';
                $room->save();
                $profile->game_total++;
                $profile->win_total++;
                $profile->rating += 15;
                $profile->save();
                $otherPlayer->profile->game_total++;
                $otherPlayer->profile->lose_total++;
                $otherPlayer->profile->rating -= 15;
                $otherPlayer->profile->save();
                $room->current_turn = null;
                return $answer = ['status' => 'success',
                'message' => 'You win!',
                'new_x' => $newX,
                'new_y' => $newY,
                'current_turn' => $room->current_turn,
                'winner' => $room->winner_order,
                'draw' => $room->draw];
                    }

                elseif($player->player_order == 2 && $room->first_finished == true)
                    {
                $room->status = 'finished';
                $room->draw = true;
                $room->save();
                $profile->game_total++;
                $profile->draw_total++;
                $profile->rating += 3;
                $profile->save();
                $otherPlayer->profile->game_total++;
                $otherPlayer->profile->draw_total++;
                $otherPlayer->profile->rating += 3;
                $otherPlayer->profile->save();
                $room->current_turn = null;
                return $answer = ['status' => 'success',
                'message' => 'Draw',
                'new_x' => $newX,
                'new_y' => $newY,
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
                $room->save();
                $profile->game_total++;
                $profile->lose_total++;
                $profile->rating -= 15;
                $profile->save();
                $otherPlayer->profile->game_total++;
                $otherPlayer->profile->win_total++;
                $otherPlayer->profile->rating += 15;
                $otherPlayer->profile->save();
                $room->current_turn = null;
                return $answer = ['status' => 'success',
                'message' => 'You lose!',
                'new_x' => $newX,
                'new_y' => $newY,
                'current_turn' => $room->current_turn,
                'winner' => $room->winner_order,
                'draw' => $room->draw];
            }
            else return $answer = [];
    }
}