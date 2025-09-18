<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingsHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'setting_id',
        'old_value',
        'new_value',
        'changed_by',
        'changed_at',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    public function setting(): BelongsTo
    {
        return $this->belongsTo(SettingsItem::class, 'setting_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('changed_at', '>=', now()->subDays($days));
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }

    public function scopeBySetting($query, $settingId)
    {
        return $query->where('setting_id', $settingId);
    }
}