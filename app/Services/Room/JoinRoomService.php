<?php
namespace App\Services\Room;

use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use App\Enums\RoomStatus;
use App\Enums\RoomType;

use DomainException;

class JoinRoomService
{
    public function join(Profile $profile, string $room_type, ?string $code = null): Room
    {
        if ($profile->player != null && $profile->player->room_id != null)
            {
                throw new DomainException('Player in other room', 409);
            }

        $room = $this->selectRoomType($profile, $room_type, $code);

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

        return $room;
    }

    private function selectRoomType(Profile $profile, string $room_type, ?string $code = null): Room
    {
        $room_type = match ($room_type)
        {
            'pvplocal' => RoomType::PvPLocal,
            'pvppublic' => RoomType::PvPPublic,
        };
        
        if ($room_type == RoomType::PvPLocal)
            {
                $room = Room::with('players')->where('code', $code)
                ->where('room_type', RoomType::PvPLocal)->first();
            }
        elseif ($room_type == RoomType::PvPPublic && $code == null)
            {
                $room = Room::with('players')->where('room_type', RoomType::PvPPublic)
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