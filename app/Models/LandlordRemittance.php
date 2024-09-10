<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LandlordRemittance extends Model
{
    use HasUuids, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'paid_on' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'paid_on', 'payment_method', 'payment_reference', 'month', 'landlord.name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id', 'id');
    }
}
