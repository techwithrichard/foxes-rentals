<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Property extends Model
{
    use HasUuids, LogsActivity, SoftDeletes;

    protected $casts = [
        'is_multi_unit' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_multi_unit',
        'rent',
        'deposit',
        'is_vacant',
        'status',
        'landlord_id',
        'commission',
        'electricity_id',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'rent', 'status', 'is_vacant', 'commission', 'deposit', 'electricity_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function houses(): HasMany
    {
        return $this->hasMany(House::class)->withTrashed();
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
        return $this->belongsTo(User::class, 'landlord_id', 'id')
            ->withDefault([
                'name' => __('Multi Owned'),
            ]);
    }
}
