<?php
namespace App\Services\Room;

use App\Models\Profile;
use App\Models\Room;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoomStatus;
use Illuminate\Support\Facades\DB;


use DomainException;

class CodeboardService
{
    public function getCodeboard()
    {
        $rooms = Room::with('players')->where('is_on_codeboard', true)->get();
        return $rooms;
    }

    public function toggleCodeboard(Room $room): Room
    {
        if($room->status != RoomStatus::Waiting)
            {
                throw new DomainException('Room is not waiting status', 409);
            }

        if($room->is_on_codeboard == false)
            {
                $room->is_on_codeboard = true;
            }
            else $room->is_on_codeboard = false;

        $room->save();
        return $room;
    }
}