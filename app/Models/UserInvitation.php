<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class UserInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'role',
        'invited_by',
        'token',
        'expires_at',
        'status',
        'accepted_at',
        'accepted_by',
        'rejected_at',
        'cancelled_at',
        'cancelled_by',
        'expired_at',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expired_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user who sent the invitation
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the user who accepted the invitation
     */
    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    /**
     * Get the user who cancelled the invitation
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Check if invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    /**
     * Check if invitation is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Check if invitation is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if invitation is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if invitation is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get invitation status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            'expired' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get invitation status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'fas fa-clock',
            'accepted' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
            'cancelled' => 'fas fa-ban',
            'expired' => 'fas fa-hourglass-end',
            default => 'fas fa-question-circle',
        };
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Get invitation URL
     */
    public function getInvitationUrlAttribute(): string
    {
        return route('invitation.accept', ['token' => $this->token]);
    }

    /**
     * Scope for pending invitations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted invitations
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope for rejected invitations
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for cancelled invitations
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for expired invitations
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
                    ->where('status', 'pending');
    }

    /**
     * Scope for valid invitations (not expired)
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope for invitations by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for invitations by inviter
     */
    public function scopeByInviter($query, int $userId)
    {
        return $query->where('invited_by', $userId);
    }
}

