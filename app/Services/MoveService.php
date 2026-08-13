<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\CheckResultService;
use App\Http\Resources\RoomResource;
use Illuminate\Support\Facades\DB;

use DomainException;

class MoveService
{
    private CheckResultService $checkResultService;

    public function __construct(CheckResultService $checkResultService)
    {
        $this->checkResultService = $checkResultService;
    }

    public function move(Profile $profile, string $code, int $new_x, int $new_y): array
    {
        $player = $profile->player;
        $room = Room::with('players')->where('code', $code)->first();
        if(!$room)
            {
                throw new DomainException('Code wasnt transmitted', 404);
            }
        $otherPlayer = $room->players->where('id', '!=', $player->id)->first();

        if($room->status != 'active')
            {
                throw new DomainException('Game is not active', 409);
            }

        if ($player->player_order != $room->current_turn)
            {
                throw new DomainException('Its not your move now', 409); 
            }

        if ($otherPlayer == null)
            {
                throw new DomainException('Not other player', 409);
            }
        
        if ($room->maze[$new_y][$new_x] != 0)
            {
                throw new DomainException('Theres a wall there', 409); 
            }

        if (abs($player->x - $new_x) > 1 || abs($player->y - $new_y) > 1)
            {
                throw new DomainException('Incorrect: you can only move one cell', 409); 
            }

        $player->x = $new_x;
        $player->y = $new_y;
        
        $player->save();

        $answer = $this->checkResultService->checkResultMove($player, $otherPlayer, $room, $profile);
        if($answer != null)
            {
                return $answer;
            }

        $this->changeOfTurn($player, $room);  
        $room->save();

        return ['status' => 'success',
        'message' => 'Move made',
        'data' => new RoomResource($room)];
    }

    private function changeOfTurn(Player $player, Room $room): void
    {
        if($player->player_order == 1)
            {
                $room->current_turn = 2;
            }
            else
                {
                    $room->turn_total++;
                    $room->current_turn = 1;
                }
    }
}