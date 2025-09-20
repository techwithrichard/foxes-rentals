<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasUuids;
    protected $fillable = [
        'voucher_number',
        'landlord_id',
        'property_id',
        'house_id',
        'amount',
        'status',
        'description',
        'date',
        'reference_number',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'voucher_date' => 'date',
    ];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(VoucherItem::class);

    }

    public function documents(): HasMany
    {
        return $this->hasMany(VoucherDocument::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }
}
