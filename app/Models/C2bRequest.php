<?php

namespace App\Models;

use App\Enums\ReconciliationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class C2bRequest extends Model
{
    protected $fillable = [
        'transaction_id',
        'amount',
        'phone_number',
        'account_number',
        'status',
        'callback_data',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    //casts reconciliation_status to ReconciliationStatusEnum
    protected $casts = [
        'reconciliation_status' => ReconciliationStatusEnum::class,
    ];


}
