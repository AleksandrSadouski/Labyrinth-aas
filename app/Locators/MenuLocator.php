<?php

namespace App\Locators;

use App\Enums\RoomType;
use App\Models\Profile;
use App\Services\Room\CreateRoomService;

use DomainException;

class MenuLocator
{
    private CreateRoomService $createRoomService;

    public function __construct(CreateRoomService $createRoomService)
    {
        $this->createRoomService = $createRoomService;
    }

    public function create(Profile $profile, RoomType $room_type, int $size, float $branch_weight, float $hallway_weight)
    {
        switch($room_type)
        {
            case RoomType::PvPLocal:
                return $this->createRoomService->create($profile, $room_type, $size, $branch_weight, $hallway_weight);
            case RoomType::PvPPublic:
                throw new DomainException('Impossible: PvPPublic room cannot be created, press the join button', 409);
            case RoomType::SP:
                return $this->createRoomService->create($profile, $room_type, $size, $branch_weight, $hallway_weight);
        }
    }
}