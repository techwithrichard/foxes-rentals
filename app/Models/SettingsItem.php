<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class SettingsItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'group_id',
        'key',
        'value',
        'type',
        'validation_rules',
        'description',
        'is_encrypted',
        'is_required',
        'default_value',
        'options',
        'placeholder',
        'is_active',
        'order_index'
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'validation_rules' => 'array',
        'options' => 'array',
        'order_index' => 'integer'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingsGroup::class, 'group_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(SettingsHistory::class, 'setting_id')->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEncrypted($query)
    {
        return $query->where('is_encrypted', true);
    }

    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value;
            }
        }
        return $value;
    }

    public function setValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    public function getFormattedValueAttribute()
    {
        $value = $this->value;
        
        switch ($this->type) {
            case 'boolean':
                return (bool) $value;
            case 'number':
                return is_numeric($value) ? (float) $value : 0;
            case 'json':
                return is_string($value) ? json_decode($value, true) : $value;
            default:
                return $value;
        }
    }

    public function validateValue($value)
    {
        if ($this->validation_rules) {
            $validator = validator(['value' => $value], ['value' => $this->validation_rules]);
            return $validator->passes();
        }
        return true;
    }
}