<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequest extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id', 'property_type', 'unit_id', 'requested_by',
        'assigned_to', 'priority', 'status', 'category', 'title',
        'description', 'images', 'requested_date', 'scheduled_date',
        'completed_date', 'estimated_cost', 'actual_cost', 'notes',
        'tenant_notes', 'maintainer_notes', 'is_emergency',
        'requires_permission', 'permission_granted', 'permission_granted_by',
        'permission_granted_date', 'follow_up_required', 'follow_up_date'
    ];

    protected $casts = [
        'requested_date' => 'datetime', 'scheduled_date' => 'datetime',
        'completed_date' => 'datetime', 'permission_granted_date' => 'datetime',
        'follow_up_date' => 'datetime', 'is_emergency' => 'boolean',
        'requires_permission' => 'boolean', 'permission_granted' => 'boolean',
        'follow_up_required' => 'boolean', 'images' => 'array',
        'estimated_cost' => 'decimal:2', 'actual_cost' => 'decimal:2'
    ];

    public function property(): MorphTo
    {
        return $this->morphTo();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(RentalUnit::class, 'unit_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function permissionGrantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'permission_granted_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getFormattedEstimatedCostAttribute()
    {
        return 'Kshs. ' . number_format($this->estimated_cost, 2);
    }

    public function getFormattedActualCostAttribute()
    {
        return 'Kshs. ' . number_format($this->actual_cost, 2);
    }
}
