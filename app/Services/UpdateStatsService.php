<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;

class UpdateStatsService
{
    public function updateStats(string $key, Player $player, Player $otherPlayer, Room $room, Profile $profile): void
    {
        switch($key)
        {
            case 'win':
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
            break;
            case 'lose':
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
            break;
            case 'draw':
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
            break;
            default:
            break;
        }
    }
}