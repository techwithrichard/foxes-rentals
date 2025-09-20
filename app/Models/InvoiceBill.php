<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBill extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_id',
        'bill_name',
        'amount',
        'description',
        'due_date',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
