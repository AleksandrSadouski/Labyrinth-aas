<?php
namespace App\Services\SP;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\SP\SPCheckResultService;
use Illuminate\Support\Facades\DB;
use App\Enums\RoomStatus;

use DomainException;

class SPMoveService
{
    private SPCheckResultService $spCheckResultService;

    private const MAX_MOVE_STEP = 1;

    public function __construct(SPCheckResultService $spCheckResultService)
    {
        $this->spCheckResultService = $spCheckResultService;
    }

    public function move(Profile $profile, Room $room, int $new_x, int $new_y): array
    {
        $player = $profile->player;

        if($room->status != RoomStatus::Active)
            {
                throw new DomainException('Game is not active', 409);
            }

        if ($room->maze[$new_y][$new_x] != 0)
            {
                throw new DomainException('Theres a wall there', 409); 
            }

        if (abs($player->x - $new_x) > self::MAX_MOVE_STEP || abs($player->y - $new_y) > self::MAX_MOVE_STEP)
            {
                throw new DomainException('Incorrect: you can only move one cell', 409); 
            }

        $player->x = $new_x;
        $player->y = $new_y;
        
        $player->save();

        $answer = $this->spCheckResultService->checkResultMove($player, $room, $profile);
        if($answer != null)
            {
                return $answer;
            }

        $this->changeOfTurn($room);  
        $room->save();

        return $answer = ['message' => 'Move made',
        'room' => $room];
    }

    private function changeOfTurn(Room $room): void
    {
        $room->turn_total++;
    }
}