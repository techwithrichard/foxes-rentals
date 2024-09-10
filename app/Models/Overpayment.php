<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Overpayment extends Model
{

    use HasUuids, LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'tenant.name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id', 'id');
    }
}
