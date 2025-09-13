<?php

namespace App\Models;

use App\Enums\MpesaStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StkRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'detailed_status' => MpesaStatusEnum::class,
        'callback_metadata' => 'array',
        'status_updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the STK request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the invoice associated with this STK request.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Update the detailed status and related fields
     */
    public function updateDetailedStatus(MpesaStatusEnum $status, int $resultCode = null, string $resultDesc = null, array $metadata = null): void
    {
        $this->update([
            'detailed_status' => $status,
            'result_code' => $resultCode,
            'result_description' => $resultDesc,
            'callback_metadata' => $metadata,
            'status_updated_at' => now(),
            'failure_reason' => $status->isFailure() ? $status->getDescription() : null,
        ]);
    }

    /**
     * Check if the request was successful
     */
    public function isSuccessful(): bool
    {
        return $this->detailed_status?->isSuccess() ?? false;
    }

    /**
     * Check if the request failed
     */
    public function isFailed(): bool
    {
        return $this->detailed_status?->isFailure() ?? false;
    }

    /**
     * Check if the request is pending
     */
    public function isPending(): bool
    {
        return $this->detailed_status?->isPending() ?? false;
    }

    /**
     * Get the status color for UI
     */
    public function getStatusColor(): string
    {
        return $this->detailed_status?->getColor() ?? 'secondary';
    }

    /**
     * Get the status icon for UI
     */
    public function getStatusIcon(): string
    {
        return $this->detailed_status?->getIcon() ?? 'ni-help';
    }

    /**
     * Get the status description
     */
    public function getStatusDescription(): string
    {
        return $this->detailed_status?->getDescription() ?? 'Unknown status';
    }
}
