<?php
namespace App\Services\Room;

use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use App\Services\Room\CreateRoomService;
use App\Enums\RoomStatus;
use App\Enums\RoomType;

use DomainException;

class JoinRoomService
{
    private CreateRoomService $createRoomService;

    public function __construct(CreateRoomService $createRoomService)
    {
        $this->createRoomService = $createRoomService;
    }

    public function join(Profile $profile, RoomType $room_type, ?string $code = null): Room
    {
        if ($profile->player != null && $profile->player->room_id != null)
            {
                throw new DomainException('Player in other room', 409);
            }

        $room = $this->selectRoomType($profile, $room_type, $code);

        if (!$room && $room_type == RoomType::PvPPublic)
            {
                return $this->createRoomService->create($profile, $room_type, 
                random_int(11, 101), random_int(0, 10)/10, random_int(0, 10)/10);
            }
        if (!$room && $room_type != RoomType::PvPPublic)
            {
                throw new DomainException('Room not found', 404);
            }
        if ($room->players->count() >= Room::MAX_PLAYERS)
            {
                throw new DomainException('Room is occupied', 409);
            }

        $room = $this->setupRoomAndPlayerforJoin($profile, $room);

        return $room;
    }

    private function selectRoomType(Profile $profile, RoomType $room_type, ?string $code = null): Room
    {      
        if ($room_type == RoomType::PvPLocal)
            {
                $room = Room::with('players')->where('code', $code)
                ->where('room_type', RoomType::PvPLocal)->where('status', RoomStatus::Waiting)->first();
            }
        elseif ($room_type == RoomType::PvPPublic && $code == null)
            {
                $room = Room::with('players')->where('room_type', RoomType::PvPPublic)
                ->where('status', RoomStatus::Waiting)
                ->inRandomOrder()->first();
            }
        return $room;
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
        $room->is_on_codeboard = false;
        $room->save();
        $room->load('players');
        });

        return $room;
    }
}