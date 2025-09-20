<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
        'tax_rate',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CustomInvoice::class);
    }
}
