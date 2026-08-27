<?php

namespace App\Services\Locators;

use App\Enums\RoomType;
use App\Models\Profile;
use App\Models\Room;
use App\Services\PvP\PvPMoveService;
use App\Services\PvP\PvPGameLeaveService;
use App\Services\SP\SPMoveService;
use App\Services\SP\SPGameLeaveService;

use DomainException;

class GameLocator
{
    private PvPMoveService $pvpMoveService;
    private SPMoveService $spMoveService;
    private PvPGameLeaveService $pvpGameLeaveService;
    private SPGameLeaveService $spGameLeaveService;

    public function __construct(
        PvPMoveService $pvpMoveService,
        SPMoveService $spMoveService,
        PvPGameLeaveService $pvpGameLeaveService,
        SPGameLeaveService $spGameLeaveService
    ) {
        $this->pvpMoveService = $pvpMoveService;
        $this->spMoveService = $spMoveService;
        $this->pvpGameLeaveService = $pvpGameLeaveService;
        $this->spGameLeaveService = $spGameLeaveService;
    }

    public function move(Profile $profile, string $code, int $new_x, int $new_y): array
    {
        $room = $this->detRoom($code);

        switch($room->room_type)
        {
            case RoomType::PvPLocal:
            case RoomType::PvPPublic:
                return $this->pvpMoveService->move($profile, $room, $new_x, $new_y);
            case RoomType::SP:
                return $this->spMoveService->move($profile, $room, $new_x, $new_y);
        }
    }

    public function exit(Profile $profile, string $code)
    {
        $room = $this->detRoom($code);

        switch($room->room_type)
        {
            case RoomType::PvPLocal:
            case RoomType::PvPPublic:
                return $this->pvpGameLeaveService->exit($profile, $room);
            case RoomType::SP:
                return $this->spGameLeaveService->exit($room);
        }
    }

    public function cancel(string $code): void
    {
        $room = $this->detRoom($code);

        switch($room->room_type)
        {
            case RoomType::PvPLocal:
            case RoomType::PvPPublic:
                $this->pvpGameLeaveService->cancel($room);
                return;
            case RoomType::SP:
                throw new DomainException('Impossible: SP cannot be cancelled', 409);
        }
    }

    private function detRoom(string $code): Room
    {
        $room = Room::with('players')->where('code', $code)->first();

        if(!$room)
            {
                throw new DomainException('Code wasnt transmitted', 404);
            }

        return $room;
    }
}