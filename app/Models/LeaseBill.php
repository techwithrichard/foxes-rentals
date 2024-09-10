<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseBill extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'lease_id', 'id');
    }
}
