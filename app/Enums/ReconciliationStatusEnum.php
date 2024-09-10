<?php

namespace App\Enums;

enum ReconciliationStatusEnum: int
{
    case PENDING = 0;
    case RECONCILED = 1;
    case IGNORED = 2;
}
