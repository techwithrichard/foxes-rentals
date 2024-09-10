<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Deposit extends Model
{
    use HasUuids, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'refund_date' => 'date',
        'refund_paid' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'status', 'tenant.name', 'refund_date', 'refund_paid', 'refund_amount'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id', 'id')
            ->withDefault(['name' => __('Archived Tenant')]);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class)->withTrashed();
    }
}
