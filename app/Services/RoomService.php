<?php
namespace App\Services;

use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Services\MazeService;
use App\Services\CodeGeneratorService;
use App\Http\Resources\RoomResource;
use Illuminate\Support\Facades\DB;
use App\Enums\RoomStatus;

use DomainException;

class RoomService
{
    private CodeGeneratorService $codeGeneratorService;

    public function __construct(CodeGeneratorService $codeGeneratorService)
    {
        $this->codeGeneratorService = $codeGeneratorService;
    }

    public function create(Profile $profile, int $size, float $branch_weight, float $hallway_weight): RoomResource
    {
        if ($profile->player != null && $profile->player->room_id != null)
            {
                throw new DomainException('Player in other room', 409);
            }
        
        $room = $this->setupRoomAndPlayerforCreate($profile, $size, $branch_weight, $hallway_weight);
        $room->load('players');

        return new RoomResource($room);
    }

    private function setupRoomAndPlayerforCreate(Profile $profile, int $size, float $branch_weight, float $hallway_weight): Room
    {
        $room = new Room();

        DB::transaction(function () use ($profile, $size, $branch_weight, $hallway_weight, $room) {
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

    public function join(Profile $profile, string $code): RoomResource
    {
        if ($profile->player != null && $profile->player->room_id != null)
            {
                throw new DomainException('Player in other room', 409);
            }

        $room = Room::with('players')->where('code', $code)->first();

        if (!$room)
            {
                throw new DomainException('Room not found', 404);
            }
        if ($room->status != RoomStatus::Waiting)
            {
                throw new DomainException('Room has already started the game', 409);
            }
        if ($room->players->count() >= Room::MAX_PLAYERS)
            {
                throw new DomainException('Room is occupied', 409);
            }

        $room = $this->setupRoomAndPlayerforJoin($profile, $room);

        return new RoomResource($room);
    }

    private function setupRoomAndPlayerforJoin(Profile $profile, Room $room): Room
    {
        DB::transaction(function () use ($profile, $room) {
        $player = new Player();
        $player->profile_id = $profile->id;
        $player->room_id = $room->id;
        $player->player_order = 2;
        $player->x = $room->entry_x;
        $player->y = $room->entry_y;
        $player->save();

        $room->status = RoomStatus::Active;
        $room->save();
        $room->load('players');
        });

        return $room;
    }
}