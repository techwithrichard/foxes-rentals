<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasUuids;

   protected $fillable = [
        'name',
        'type',
        'is_active',
        'configuration',
        'description',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
