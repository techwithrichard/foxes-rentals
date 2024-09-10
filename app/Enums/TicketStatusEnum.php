<?php

namespace App\Enums;

enum TicketStatusEnum: string
{
    case Open = 'open';
    case Closed = 'closed';
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case OnHold = 'on_hold';
}

