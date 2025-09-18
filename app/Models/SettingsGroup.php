<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettingsGroup extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'order_index',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_index' => 'integer'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SettingsCategory::class, 'category_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SettingsItem::class, 'group_id')->orderBy('order_index');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(SettingsItem::class, 'group_id')
            ->where('is_active', true)
            ->orderBy('order_index');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }
}