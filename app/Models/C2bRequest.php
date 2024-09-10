<?php

namespace App\Models;

use App\Enums\ReconciliationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class C2bRequest extends Model
{
    protected $guarded = [];

    //casts reconciliation_status to ReconciliationStatusEnum
    protected $casts = [
        'reconciliation_status' => ReconciliationStatusEnum::class,
    ];


}
