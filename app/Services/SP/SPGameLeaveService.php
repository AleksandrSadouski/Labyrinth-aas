<?php
namespace App\Services\SP;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Enums\RoomStatus;

class SPGameLeaveService
{

    public function exit(Room $room): void
    {     
        $room->delete();
    }
}