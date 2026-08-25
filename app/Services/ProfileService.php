<?php
namespace App\Services;

use App\Models\Profile;
use App\Services\UpdateStatsService;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoomStatus;
use Illuminate\Support\Facades\DB;


use DomainException;

class ProfileService
{
    private UpdateStatsService $updateStatsService;

    public function __construct(UpdateStatsService $updateStatsService)
    {
        $this->updateStatsService = $updateStatsService;
    }

    public function delete(Profile $profile, string $password): void
    {
        if (!Hash::check($password, $profile->password))
            {
                throw new DomainException('Incorrect password', 401);
            }
            
        if ($profile->player)
            {
                throw new DomainException('Impossible: player in room', 409);
            }
        $profile->tokens()->delete();
        $profile->delete();
    }

    public function rename(Profile $profile, string $newName): Profile
    {
        $profile->name = $newName;
        $profile->save();
        return $profile;
    }

    public function exit(Profile $profile): void
    {
        if ($profile->player)
            {
                throw new DomainException('Impossible: player in room', 409);
            }
        $profile->currentAccessToken()->delete();
    }

    public function changePassword(Profile $profile, string $old_password, string $new_password): void
    {
        if (!Hash::check($old_password, $profile->password))
            {
                throw new DomainException('Incorrect old password', 401);
            }

        $profile->password = Hash::make($new_password);
        $profile->save();
    }

    public function showProfile(string $profile_name): Profile
    {
        $profile = Profile::where('name', $profile_name)->first();
        if (!$profile)
            {
                throw new DomainException('Not found profile', 404);
            }

        return $profile;
    }

    public function reset(Profile $profile, string $password): Profile
    {
        if (!Hash::check($password, $profile->password))
            {
                throw new DomainException('Incorrect password', 401);
            }
        
        if ($profile->player)
            {
                throw new DomainException('Impossible: player in room', 409);
            }
            
        $profile->rating = 150;
        $profile->game_total = 0;
        $profile->win_total = 0;
        $profile->draw_total = 0;
        $profile->lose_total = 0;
        $profile->gameHistories->delete();
        $profile->save();

        return $profile;
    }
}