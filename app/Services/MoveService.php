<?php
namespace App\Services;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\CheckResultService;
use App\Http\Resources\RoomResource;

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
        $player->x = $new_x;
        $player->y = $new_y;
        $room = Room::with('players')->where('code', $code)->first();
        if(!$room)
            {
                throw new DomainException('Code wasnt transmitted', 404);
            }
        $otherPlayer = $room->players->where('id', '!=', $player->id)->first();

        $this->checkProblems($player, $otherPlayer, $room);
        
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

    private function checkProblems(Player $player, Player $otherPlayer, Room $room): void
    {
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
        
        if ($room->maze[$player->y][$player->x] != 0)
            {
                throw new DomainException('Theres a wall there', 409); 
            }
    }
}