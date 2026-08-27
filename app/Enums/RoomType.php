<?php

namespace App\Enums;

enum RoomType: string
{
    case PvPLocal = 'pvplocal';
    case PvPPublic = 'pvppublic';
    case SP = 'sp';
}
