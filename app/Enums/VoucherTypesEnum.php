<?php

namespace App\Enums;

enum VoucherTypesEnum: string
{
    case PAYMENT = 'PAYMENT';
    case RECEIPT = 'RECEIPT';
}
