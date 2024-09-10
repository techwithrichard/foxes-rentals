<?php

namespace App\Enums;

enum PropertyStatusEnum: int
{
    case VACANT = 0;
    case OCCUPIED = 1;
    case MULTI_UNIT = 2;
    case UNDER_MAINTENANCE = 3;
}
