<?php
namespace App\Services\Menu;

use App\Models\Profile;
use Illuminate\Support\Facades\Cache;

class LeaderboardService
{
    public function detLeaderboard(string $type_top): array
    {
        return match($type_top)
        {
            'rating' => ['name_top' => 'top_rating', 'current_column' => 'rating', 'limit' => 10],
            'sp_game_completed' => ['name_top' => 'top_sp_game_completed', 'current_column' => 'sp_game_completed', 'limit' => 10],
        };
    }

    public function createLeaderboard(string $name_top, string $current_column, int $limit)
    {
        $profiles = Cache::remember($name_top, 3600, function () use ($current_column, $limit) {
            return Profile::orderBy($current_column, 'desc')->select('name', $current_column)->limit($limit)->get();
            });
        return $profiles;
    }

    public function getLeaderboard(string $type_top)
    {
        $data = $this->detLeaderboard($type_top);
        return $this->createLeaderboard($data['name_top'], $data['current_column'], $data['limit']);
    }
}