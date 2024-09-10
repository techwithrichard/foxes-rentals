<?php

namespace App\Enums;

enum HouseStatusEnum: int
{
    case VACANT = 0;
    case OCCUPIED = 1;
    case UNDER_MAINTENANCE = 2;
}
