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
                $this->updateRoom($key, $player, $room);
                $this->updateWinner($profile);
                $this->updateLoser($otherPlayer->profile);
            break;
            case 'lose':
                $this->updateRoom($key, $otherPlayer, $room);
                $this->updateWinner($otherPlayer->profile);
                $this->updateLoser($profile);
            break;
            case 'draw':
                $this->updateRoom($key, $player, $room);
                $this->updateDrawer($profile);
                $this->updateDrawer($otherPlayer->profile);
            break;
            default:
            break;
        }
    }

    public function updateRoom(string $key, Player $player, Room $room): void
    {
        $room->status = 'finished';
        $room->current_turn = null;

        if($key == 'draw')
        {$room->draw = true;}
        else $room->winner_order = $player->player_order;

        $room->save();
    }

    public function updateWinner(Profile $profile): void
    {
        $profile->game_total++;
        $profile->win_total++;
        $profile->rating += 15;
        $profile->save();
    }

    public function updateLoser(Profile $profile): void
    {
        $profile->game_total++;
        $profile->lose_total++;
        $profile->rating -= 15;
        $profile->save();
    }

    public function updateDrawer(Profile $profile): void
    {
        $profile->game_total++;
        $profile->draw_total++;
        $profile->rating += 3;
        $profile->save();
    }
}