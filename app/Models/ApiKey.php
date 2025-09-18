<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class ApiKey extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'service_name',
        'key_type',
        'environment',
        'encrypted_value',
        'description',
        'is_active',
        'last_used_at',
        'last_used_by',
        'created_by',
        'expires_at',
        'usage_count',
        'rate_limit',
        'allowed_ips'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'usage_count' => 'integer',
        'rate_limit' => 'integer',
        'allowed_ips' => 'array'
    ];

    protected $hidden = [
        'encrypted_value'
    ];

    /**
     * Get the decrypted value of the API key
     */
    public function getDecryptedValueAttribute(): string
    {
        try {
            return Crypt::decryptString($this->encrypted_value);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Set the encrypted value of the API key
     */
    public function setValueAttribute(string $value): void
    {
        $this->attributes['encrypted_value'] = Crypt::encryptString($value);
    }

    /**
     * Get the user who created this API key
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last used this API key
     */
    public function lastUsedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_used_by');
    }

    /**
     * Scope to get active API keys
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get API keys for specific environment
     */
    public function scopeForEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    /**
     * Scope to get API keys by service
     */
    public function scopeByService($query, string $serviceName)
    {
        return $query->where('service_name', $serviceName);
    }

    /**
     * Check if API key is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if API key is valid for use
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Mask the API key for display purposes
     */
    public function getMaskedValueAttribute(): string
    {
        $value = $this->decrypted_value;
        $length = strlen($value);
        
        if ($length <= 8) {
            return str_repeat('*', $length);
        }
        
        return substr($value, 0, 4) . str_repeat('*', $length - 8) . substr($value, -4);
    }

    /**
     * Get the service display name
     */
    public function getServiceDisplayNameAttribute(): string
    {
        $serviceNames = [
            'mpesa' => 'M-PESA',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'sendgrid' => 'SendGrid',
            'twilio' => 'Twilio',
            'aws' => 'AWS',
            'google' => 'Google',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter'
        ];

        return $serviceNames[strtolower($this->service_name)] ?? ucfirst($this->service_name);
    }

    /**
     * Get key type display name
     */
    public function getKeyTypeDisplayNameAttribute(): string
    {
        $typeNames = [
            'api_key' => 'API Key',
            'secret' => 'Secret Key',
            'token' => 'Access Token',
            'webhook_url' => 'Webhook URL',
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret'
        ];

        return $typeNames[$this->key_type] ?? ucfirst(str_replace('_', ' ', $this->key_type));
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($apiKey) {
            if (!$apiKey->created_by && auth()->check()) {
                $apiKey->created_by = auth()->id();
            }
        });
    }
}
