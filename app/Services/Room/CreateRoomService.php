<?php
namespace App\Services\Room;

use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\Shared\MazeService;
use App\Services\Shared\CodeGeneratorService;
use Illuminate\Support\Facades\DB;
use App\Enums\RoomStatus;
use App\Enums\RoomType;

use DomainException;

class CreateRoomService
{
    private CodeGeneratorService $codeGeneratorService;

    public function __construct(CodeGeneratorService $codeGeneratorService)
    {
        $this->codeGeneratorService = $codeGeneratorService;
    }

    public function create(Profile $profile, RoomType $room_type, int $size, float $branch_weight, float $hallway_weight): Room
    {
        if ($profile->player != null && $profile->player->room_id != null)
            {
                throw new DomainException('Player in other room', 409);
            }

        $room = $this->setupRoomAndPlayerforCreate($profile, $room_type, $size, $branch_weight, $hallway_weight);

        return $room;
    }

    private function setupRoomAndPlayerforCreate(Profile $profile, RoomType $room_type, int $size, float $branch_weight, float $hallway_weight): Room
    {
        $room = new Room();

        DB::transaction(function () use ($profile, $room_type, $size, $branch_weight, $hallway_weight, $room) {
        $room->room_type = $room_type;
        if ($room->room_type == RoomType::SP)
            {
                $room->status = RoomStatus::Active;
            }
        $room->size = $size;
        $room->branch_weight = $branch_weight;
        $room->hallway_weight = $hallway_weight;
        $maze = new MazeService($room->size, $room->branch_weight, $room->hallway_weight);
        $room->maze = $maze->getMaze();
        $room->code = $this->codeGeneratorService->generate();
        $entry = $maze->getEntry();
        $room->entry_x = $entry[1];
        $room->entry_y = $entry[0];
        $exit = $maze->getExit();
        $room->exit_x = $exit[1];
        $room->exit_y = $exit[0];
        $room->save();

        $player = new Player();
        $player->profile_id = $profile->id;
        $player->player_order = 1;
        $player->x = $room->entry_x;
        $player->y = $room->entry_y;
        $player->room_id = $room->id;
        $player->save();

        $room->load('players');
        });

        return $room;
    }
}