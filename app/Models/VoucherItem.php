<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherItem extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }
}
