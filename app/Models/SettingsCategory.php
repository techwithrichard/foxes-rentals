<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettingsCategory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order_index',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_index' => 'integer'
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(SettingsGroup::class, 'category_id')->orderBy('order_index');
    }

    public function activeGroups(): HasMany
    {
        return $this->hasMany(SettingsGroup::class, 'category_id')
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