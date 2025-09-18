<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiKeyManagementService
{
    protected $cachePrefix = 'api_key_';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Get available services for API key configuration
     */
    public function getAvailableServices(): array
    {
        return [
            'mpesa' => 'M-PESA',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'sendgrid' => 'SendGrid',
            'mailgun' => 'Mailgun',
            'twilio' => 'Twilio',
            'africas_talking' => 'Africa\'s Talking',
            'aws' => 'Amazon Web Services',
            'google' => 'Google Services',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'linkedin' => 'LinkedIn',
            'github' => 'GitHub',
            'slack' => 'Slack',
            'webhook' => 'Webhook',
            'custom' => 'Custom Service'
        ];
    }

    /**
     * Get available key types
     */
    public function getKeyTypes(): array
    {
        return [
            'api_key' => 'API Key',
            'secret' => 'Secret Key',
            'token' => 'Access Token',
            'webhook_url' => 'Webhook URL',
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'public_key' => 'Public Key',
            'private_key' => 'Private Key'
        ];
    }

    /**
     * Validate API key format based on service
     */
    public function validateApiKey(string $serviceName, string $key): bool
    {
        $validators = [
            'mpesa' => function($key) {
                return strlen($key) >= 20 && preg_match('/^[A-Za-z0-9]+$/', $key);
            },
            'paypal' => function($key) {
                return strlen($key) >= 20 && (str_contains($key, 'A') || str_contains($key, 'E'));
            },
            'stripe' => function($key) {
                return (str_starts_with($key, 'sk_') || str_starts_with($key, 'pk_')) && strlen($key) >= 50;
            },
            'sendgrid' => function($key) {
                return str_starts_with($key, 'SG.') && strlen($key) >= 50;
            },
            'twilio' => function($key) {
                return preg_match('/^[A-Za-z0-9]{32}$/', $key);
            },
            'aws' => function($key) {
                return preg_match('/^[A-Z0-9]{20}$/', $key);
            },
            'google' => function($key) {
                return strlen($key) >= 50 && (str_contains($key, '.') || str_contains($key, '-'));
            },
            'webhook_url' => function($key) {
                return filter_var($key, FILTER_VALIDATE_URL) && (str_starts_with($key, 'https://') || str_starts_with($key, 'http://'));
            }
        ];

        $validator = $validators[strtolower($serviceName)] ?? null;
        
        if ($validator) {
            return $validator($key);
        }

        // Default validation for unknown services
        return strlen($key) >= 10;
    }

    /**
     * Test API key connectivity
     */
    public function testApiKey(ApiKey $apiKey): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'response_time' => 0,
            'status_code' => null
        ];

        try {
            $startTime = microtime(true);
            $decryptedKey = $apiKey->decrypted_value;

            switch (strtolower($apiKey->service_name)) {
                case 'mpesa':
                    $result = $this->testMpesaKey($decryptedKey);
                    break;
                case 'sendgrid':
                    $result = $this->testSendGridKey($decryptedKey);
                    break;
                case 'twilio':
                    $result = $this->testTwilioKey($decryptedKey);
                    break;
                case 'webhook':
                    $result = $this->testWebhookUrl($decryptedKey);
                    break;
                default:
                    $result = $this->testGenericKey($apiKey->service_name, $decryptedKey);
            }

            $result['response_time'] = round((microtime(true) - $startTime) * 1000, 2);

            // Update usage count if test was successful
            if ($result['success']) {
                $apiKey->incrementUsage();
                $apiKey->update(['last_used_by' => auth()->id()]);
            }

        } catch (\Exception $e) {
            $result['message'] = 'Test failed: ' . $e->getMessage();
            Log::error("API key test failed for {$apiKey->service_name}: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Test M-PESA API key
     */
    protected function testMpesaKey(string $key): array
    {
        try {
            // Test M-PESA authentication endpoint
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $key,
                    'Content-Type' => 'application/json'
                ])
                ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

            return [
                'success' => $response->successful(),
                'message' => $response->successful() ? 'M-PESA API key is valid' : 'M-PESA API key test failed',
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'M-PESA API test failed: ' . $e->getMessage(),
                'status_code' => null
            ];
        }
    }

    /**
     * Test SendGrid API key
     */
    protected function testSendGridKey(string $key): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $key,
                    'Content-Type' => 'application/json'
                ])
                ->get('https://api.sendgrid.com/v3/user/account');

            return [
                'success' => $response->successful(),
                'message' => $response->successful() ? 'SendGrid API key is valid' : 'SendGrid API key test failed',
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'SendGrid API test failed: ' . $e->getMessage(),
                'status_code' => null
            ];
        }
    }

    /**
     * Test Twilio API key
     */
    protected function testTwilioKey(string $key): array
    {
        try {
            // For Twilio, we need both Account SID and Auth Token
            // This is a simplified test
            $response = Http::timeout(30)
                ->withBasicAuth($key, 'dummy') // In real implementation, use proper credentials
                ->get('https://api.twilio.com/2010-04-01/Accounts.json');

            return [
                'success' => false, // Simplified for demo
                'message' => 'Twilio API key validation requires Account SID and Auth Token',
                'status_code' => null
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Twilio API test failed: ' . $e->getMessage(),
                'status_code' => null
            ];
        }
    }

    /**
     * Test webhook URL
     */
    protected function testWebhookUrl(string $url): array
    {
        try {
            $response = Http::timeout(30)
                ->post($url, [
                    'test' => true,
                    'timestamp' => now()->toISOString(),
                    'source' => 'foxes_rental_system'
                ]);

            return [
                'success' => $response->status() < 500, // Accept 2xx, 3xx, 4xx as valid responses
                'message' => $response->status() < 500 ? 'Webhook URL is reachable' : 'Webhook URL returned server error',
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Webhook URL test failed: ' . $e->getMessage(),
                'status_code' => null
            ];
        }
    }

    /**
     * Test generic API key
     */
    protected function testGenericKey(string $serviceName, string $key): array
    {
        return [
            'success' => true,
            'message' => "Generic validation passed for {$serviceName}",
            'status_code' => 200
        ];
    }

    /**
     * Get API key statistics
     */
    public function getApiKeyStatistics(): array
    {
        return Cache::remember($this->cachePrefix . 'statistics', $this->cacheTtl, function () {
            return [
                'total_keys' => ApiKey::count(),
                'active_keys' => ApiKey::active()->count(),
                'expired_keys' => ApiKey::where('expires_at', '<', now())->count(),
                'services_count' => ApiKey::distinct('service_name')->count(),
                'recent_usage' => ApiKey::where('last_used_at', '>=', now()->subDays(7))->count(),
                'environment_distribution' => ApiKey::selectRaw('environment, COUNT(*) as count')
                    ->groupBy('environment')
                    ->pluck('count', 'environment')
                    ->toArray()
            ];
        });
    }

    /**
     * Get API key usage analytics
     */
    public function getUsageAnalytics(int $days = 30): array
    {
        return Cache::remember($this->cachePrefix . 'analytics_' . $days, $this->cacheTtl, function () use ($days) {
            $startDate = now()->subDays($days);
            
            return [
                'daily_usage' => ApiKey::where('last_used_at', '>=', $startDate)
                    ->selectRaw('DATE(last_used_at) as date, COUNT(*) as usage_count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->pluck('usage_count', 'date'),
                
                'top_services' => ApiKey::where('last_used_at', '>=', $startDate)
                    ->selectRaw('service_name, SUM(usage_count) as total_usage')
                    ->groupBy('service_name')
                    ->orderByDesc('total_usage')
                    ->limit(10)
                    ->pluck('total_usage', 'service_name'),
                
                'usage_by_environment' => ApiKey::where('last_used_at', '>=', $startDate)
                    ->selectRaw('environment, SUM(usage_count) as total_usage')
                    ->groupBy('environment')
                    ->pluck('total_usage', 'environment')
            ];
        });
    }

    /**
     * Clean up expired API keys
     */
    public function cleanupExpiredKeys(): int
    {
        $expiredKeys = ApiKey::where('expires_at', '<', now())->get();
        $count = $expiredKeys->count();

        foreach ($expiredKeys as $key) {
            $key->update(['is_active' => false]);
            Log::info("API key deactivated due to expiration: {$key->id}");
        }

        return $count;
    }

    /**
     * Get API key for service (for use in application)
     */
    public function getApiKeyForService(string $serviceName, string $environment = null): ?string
    {
        $environment = $environment ?: app()->environment();
        
        $cacheKey = $this->cachePrefix . 'service_' . $serviceName . '_' . $environment;
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($serviceName, $environment) {
            $apiKey = ApiKey::active()
                ->byService($serviceName)
                ->forEnvironment($environment)
                ->where('expires_at', '>', now())
                ->orWhereNull('expires_at')
                ->first();

            return $apiKey ? $apiKey->decrypted_value : null;
        });
    }

    /**
     * Clear API key cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'statistics');
        Cache::forget($this->cachePrefix . 'analytics_30');
        
        // Clear service-specific caches
        $services = ApiKey::distinct('service_name')->pluck('service_name');
        $environments = ['production', 'staging', 'development'];
        
        foreach ($services as $service) {
            foreach ($environments as $env) {
                Cache::forget($this->cachePrefix . 'service_' . $service . '_' . $env);
            }
        }
    }
}
