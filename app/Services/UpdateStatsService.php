<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\EloService;
use Illuminate\Support\Facades\DB;

class UpdateStatsService
{
    private EloService $eloService;

    public function __construct(EloService $eloService)
    {
        $this->eloService = $eloService;
    } 

    public function updateStats(string $key, Player $player, Player $otherPlayer, Room $room, Profile $profile): void
    {
        DB::transaction(function () use ($key, $player, $otherPlayer, $room, $profile) {
        switch($key)
        {
            case 'win':
                $this->updateRoom($key, $player, $room);
                $this->updateWinner($key, $profile, $otherPlayer->profile->rating);
                $this->updateLoser($key, $otherPlayer->profile, $profile->rating);
            break;
            case 'lose':
                $this->updateRoom($key, $otherPlayer, $room);
                $this->updateWinner($key, $otherPlayer->profile, $profile->rating);
                $this->updateLoser($key, $profile, $otherPlayer->profile->rating);
            break;
            case 'draw':
                $this->updateRoom($key, $player, $room);
                $this->updateDrawer($key, $profile, $otherPlayer->profile->rating);
                $this->updateDrawer($key, $otherPlayer->profile, $profile->rating);
            break;
            default:
            break;
        }

        $room->save();
        $profile->save();
        $otherPlayer->profile->save();
        });
    }

    public function updateRoom(string $key, Player $player, Room $room): void
    {
        $room->status = 'finished';
        $room->current_turn = 0;

        if($key == 'draw')
        {$room->draw = true;}
        else $room->winner_order = $player->player_order;
    }

    public function updateWinner(string $key, Profile $profileWinner, int $ratingLoser): void
    {
        $profileWinner->game_total++;
        $profileWinner->win_total++;
        $profileWinner->rating = $this->eloService->calcRating($key, $profileWinner->rating, $ratingLoser);
    }

    public function updateLoser(string $key, Profile $profileLoser, int $ratingWinner): void
    {
        $profileLoser->game_total++;
        $profileLoser->lose_total++;
        $profileLoser->rating = $this->eloService->calcRating($key, $profileLoser->rating, $ratingWinner);
    }

    public function updateDrawer(string $key, Profile $profileDrawer, int $ratingOtherDrawer): void
    {
        $profileDrawer->game_total++;
        $profileDrawer->draw_total++;
        $profileDrawer->rating = $this->eloService->calcRating($key, $profileDrawer->rating, $ratingOtherDrawer);
    }
}