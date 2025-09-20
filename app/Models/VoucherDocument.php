<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherDocument extends Model
{
    use HasUuids;

    protected $fillable = [
        'voucher_id',
        'document_name',
        'document_path',
        'document_type',
        'file_size',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
