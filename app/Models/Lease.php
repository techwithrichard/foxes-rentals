<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lease extends Model
{
    use SoftDeletes;
    use HasUuids, LogsActivity;


    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'status' => 'string',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['start_date', 'end_date', 'rent', 'rent_cycle', 'invoice_generation_day', 'tenant.name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id', 'id')
            ->withDefault(['name' => 'Archived Tenant']);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class, 'house_id', 'id')
            ->withDefault(['name' => '',]);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(LeaseBill::class, 'lease_id', 'id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LeaseDocument::class);
    }

    public function deposit(): HasOne
    {
        return $this->hasOne(Deposit::class);
    }

}
