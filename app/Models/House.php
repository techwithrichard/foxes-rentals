<?php

namespace App\Models;

use App\Enums\HouseStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class House extends Model
{
    use HasUuids, LogsActivity;

    use HasFactory;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'rent', 'status', 'commission', 'deposit', 'electricity_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class)->withTrashed();
    }

    public function lease(): HasOne
    {
        return $this->hasOne(Lease::class)
            ->ofMany([
                'id' => 'max',
            ], function ($query) {
                $query->whereNull('deleted_at');
            });


    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id', 'id');
    }

    //getter to get house_status based on HOUSE_STATUS enum
    public function getHouseStatusAttribute(): string
    {
        return HouseStatusEnum::from($this->status)->name;
    }

}
