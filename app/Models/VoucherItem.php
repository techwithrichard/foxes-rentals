<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'voucher_id',
        'description',
        'amount',
        'quantity',
        'unit_price',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }
}
