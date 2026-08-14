<?php
namespace App\Services;

use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Models\Message;
use App\Http\Resources\RoomResource;

use DomainException;

class PollingService
{
    public function poll(string $code): RoomResource
    {
        $room = Room::with(['players.profile', 'players.messages'])->where('code', $code)->first();
        if(!$room)
            {
                throw new DomainException('Code wasnt transmitted', 404);
            }

        return new RoomResource($room);
    }
}