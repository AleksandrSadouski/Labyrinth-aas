<?php
namespace App\Services\SP;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\Shared\RatingService;
use Illuminate\Support\Facades\DB;
use App\Enums\RoomStatus;

class SPUpdateStatsService
{
    private RatingService $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    } 

    public function updateStats(Room $room, Profile $profile): void
    {
        DB::transaction(function () use ($room, $profile) {

        $this->updateRoom($room);
        $this->updateWinner($profile, $room);

        $room->save();
        $profile->save();
        });
    }

    public function updateRoom(Room $room): void
    {
        $room->status = RoomStatus::Finished;
        $room->current_turn = 0;
    }

    public function updateWinner(Profile $profileWinner, Room $room): void
    {
        $profileWinner->sp_game_completed++;
        $profileWinner->sp_rating = $this->ratingService->calcSadovskyRating($profileWinner->sp_rating, 
        $profileWinner->sp_game_completed, $room->size, $room->branch_weight, $room->hallway_weight);
    }
}