<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyInquiry extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id', 'property_type', 'inquirer_name', 'inquirer_email',
        'inquirer_phone', 'message', 'status', 'priority', 'source',
        'assigned_to', 'response', 'response_date', 'follow_up_date',
        'notes', 'is_qualified', 'budget_range', 'move_in_date',
        'special_requirements'
    ];

    protected $casts = [
        'is_qualified' => 'boolean', 'response_date' => 'datetime',
        'follow_up_date' => 'datetime', 'move_in_date' => 'date',
        'special_requirements' => 'array'
    ];

    public function property(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeQualified($query)
    {
        return $query->where('is_qualified', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
