<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Waiting = 'waiting';
    case Active = 'active';
    case First_finished = 'first_finished';
    case Finished = 'finished';
}
