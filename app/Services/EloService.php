<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;

class EloService
{

    public function calcRating(string $key, int $R_A, int $R_B): int
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
}