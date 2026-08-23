<?php
namespace App\Services;

use App\Models\Profile;
use Illuminate\Support\Facades\Cache;

class LeaderboardService
{
    public function getProfiles(string $nameTop, string $currentColumn, int $limit)
    {
        $profiles = Cache::remember($nameTop, 240, function () use ($currentColumn, $limit) {
            return Profile::orderBy($currentColumn, 'desc')->select('name', $currentColumn)->limit($limit)->get();
            });
        return $profiles;
    }
}