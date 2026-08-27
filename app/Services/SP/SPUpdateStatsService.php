<?php
namespace App\Services\SP;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use App\Enums\RoomStatus;

class SPUpdateStatsService
{
    public function updateStats(Room $room, Profile $profile): void
    {
        DB::transaction(function () use ($room, $profile) {

        $this->updateRoom($room);
        $this->updateWinner($profile);

        $room->save();
        $profile->save();
        });
    }

    public function updateRoom(Room $room): void
    {
        $room->status = RoomStatus::Finished;
        $room->current_turn = 0;
    }

    public function updateWinner(Profile $profileWinner): void
    {
        $profileWinner->sp_game_completed++;
    }
}