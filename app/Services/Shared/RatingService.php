<?php
namespace App\Services\Shared;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;

class RatingService
{

    public function calcEloRating(string $key, int $R_A, int $R_B): int
    {
        $E_A = 1 / (1 + 10 ** (($R_B - $R_A) / 400));
        $S = match($key){
            'win' => 1,
            'draw' => 0.5,
            'lose' => 0
        };
        $K = 50;
        $R_new = $R_A + $K * ($S - $E_A);
        return round($R_new);
    }

    public function calcSadovskyRating(int $R_old, int $sp_game_completed, int $size, float $branch_weight, float $hallway_weight): int
    {
        $C = ($size/10) * (1 + $branch_weight) * (1 + $hallway_weight);
        $R_new = $R_old + $C * (1 + 10 / (10 + $sp_game_completed));
        return round($R_new);
    }
}