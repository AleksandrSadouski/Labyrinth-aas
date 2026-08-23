<?php
namespace App\Services;

use App\Models\Profile;
use App\Services\UpdateStatsService;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoomStatus;

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
            
        if ($profile->player && $profile->player->room && $profile->player->room->status == RoomStatus::Active)
            {
                $otherPlayer = $profile->player->room->players->where('id', '!=', $profile->player->id)->first();
                $this->updateStatsService->updateStats('lose', $profile->player, $otherPlayer, $profile->player->room, $profile);
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
        $profile->currentAccessToken()->delete();
    }
}