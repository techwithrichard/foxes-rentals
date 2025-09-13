<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyOffer extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_property_id', 'buyer_id', 'offer_amount', 'status',
        'offer_date', 'expiration_date', 'contingencies', 'financing_type',
        'down_payment', 'closing_date', 'special_conditions', 'notes',
        'counter_offer_amount', 'counter_offer_notes', 'accepted_date',
        'rejected_date', 'withdrawn_date', 'accepted_by', 'rejected_by'
    ];

    protected $casts = [
        'offer_date' => 'datetime', 'expiration_date' => 'datetime',
        'accepted_date' => 'datetime', 'rejected_date' => 'datetime',
        'withdrawn_date' => 'datetime', 'closing_date' => 'date',
        'offer_amount' => 'decimal:2', 'counter_offer_amount' => 'decimal:2',
        'down_payment' => 'decimal:2', 'contingencies' => 'array',
        'special_conditions' => 'array'
    ];

    public function saleProperty(): BelongsTo
    {
        return $this->belongsTo(SaleProperty::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getFormattedOfferAmountAttribute()
    {
        return 'Kshs. ' . number_format($this->offer_amount, 2);
    }
}
