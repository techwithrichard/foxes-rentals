<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';
    case PARTIALLY_PAID = 'partially_paid';
    case OVER_PAID = 'over_paid';


}
