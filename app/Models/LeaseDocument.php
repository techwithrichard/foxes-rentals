<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseDocument extends Model
{
    use HasUuids;

    protected $fillable = [
        'lease_id',
        'document_name',
        'document_path',
        'document_type',
        'file_size',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
