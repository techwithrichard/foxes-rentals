<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomInvoice extends Model
{
    use HasUuids;

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    protected $fillable = [
        'invoice_number',
        'client_name',
        'client_email',
        'amount',
        'description',
        'due_date',
        'status',
        'notes',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id', 'id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
