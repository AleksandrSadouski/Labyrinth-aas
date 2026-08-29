<?php
namespace App\Services\PvP;

use App\Models\Profile;
use App\Models\GameHistory;

class PvPGameHistoryService
{
    public function createHistory(string $key, Profile $profile, Profile $profile_opponent): void
    {
        $game_history = GameHistory::create(['profile_id' => $profile->id,
        'result' => $key,
        'name_opponent' => $profile_opponent->name,
        'pvp_rating_opponent' => $profile_opponent->pvp_rating,
        'pvp_rating' => $profile->pvp_rating]);
        $game_history->load('profile');
    }
}