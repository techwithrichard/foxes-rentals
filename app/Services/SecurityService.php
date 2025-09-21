<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SecurityService
{
    /**
     * Validate password strength
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        $score = 0;

        // Length check
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        } else {
            $score += 1;
        }

        // Uppercase check
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        } else {
            $score += 1;
        }

        // Lowercase check
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        } else {
            $score += 1;
        }

        // Number check
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        } else {
            $score += 1;
        }

        // Special character check
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        } else {
            $score += 1;
        }

        // Common password check
        if ($this->isCommonPassword($password)) {
            $errors[] = 'Password is too common, please choose a more unique password';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'score' => $score,
            'strength' => $this->getPasswordStrength($score)
        ];
    }

    /**
     * Generate secure password
     */
    public function generateSecurePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = '';
        
        // Ensure at least one character from each category
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill the rest with random characters
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Check if password is common
     */
    protected function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            'password', '123456', '123456789', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey',
            '1234567890', 'dragon', 'master', 'hello', 'freedom'
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * Get password strength level
     */
    protected function getPasswordStrength(int $score): string
    {
        if ($score <= 2) return 'weak';
        if ($score <= 3) return 'fair';
        if ($score <= 4) return 'good';
        return 'strong';
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $data = []): void
    {
        Log::channel('security')->info($event, array_merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id()
        ], $data));
    }

    /**
     * Check for suspicious activity
     */
    public function checkSuspiciousActivity(User $user): array
    {
        $suspiciousActivities = [];

        // Check for multiple failed login attempts
        $failedAttempts = Cache::get("failed_login_attempts_{$user->id}", 0);
        if ($failedAttempts > 5) {
            $suspiciousActivities[] = [
                'type' => 'multiple_failed_logins',
                'severity' => 'high',
                'message' => 'Multiple failed login attempts detected'
            ];
        }

        // Check for unusual login times
        $lastLogin = $user->last_login_at;
        if ($lastLogin && $lastLogin->hour < 6 || $lastLogin->hour > 22) {
            $suspiciousActivities[] = [
                'type' => 'unusual_login_time',
                'severity' => 'medium',
                'message' => 'Login at unusual time detected'
            ];
        }

        // Check for rapid password changes
        $passwordChanges = Cache::get("password_changes_{$user->id}", 0);
        if ($passwordChanges > 3) {
            $suspiciousActivities[] = [
                'type' => 'rapid_password_changes',
                'severity' => 'high',
                'message' => 'Rapid password changes detected'
            ];
        }

        return $suspiciousActivities;
    }

    /**
     * Implement account lockout
     */
    public function lockAccount(User $user, string $reason = 'Suspicious activity'): void
    {
        $user->update([
            'is_active' => false,
            'locked_at' => now(),
            'lock_reason' => $reason
        ]);

        $this->logSecurityEvent('account_locked', [
            'user_id' => $user->id,
            'reason' => $reason
        ]);
    }

    /**
     * Unlock account
     */
    public function unlockAccount(User $user): void
    {
        $user->update([
            'is_active' => true,
            'locked_at' => null,
            'lock_reason' => null
        ]);

        $this->logSecurityEvent('account_unlocked', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Generate secure API key
     */
    public function generateApiKey(): string
    {
        return 'foxes_' . Str::random(32) . '_' . time();
    }

    /**
     * Validate API key format
     */
    public function validateApiKey(string $apiKey): bool
    {
        return preg_match('/^foxes_[a-zA-Z0-9]{32}_\d+$/', $apiKey);
    }

    /**
     * Encrypt sensitive data
     */
    public function encryptSensitiveData(string $data): string
    {
        $key = config('app.key');
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt sensitive data
     */
    public function decryptSensitiveData(string $encryptedData): string
    {
        $key = config('app.key');
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }

    /**
     * Generate secure token
     */
    public function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrfToken(string $token): bool
    {
        return hash_equals(session()->token(), $token);
    }

    /**
     * Check IP whitelist
     */
    public function isIpWhitelisted(string $ip): bool
    {
        $whitelist = config('security.ip_whitelist', []);
        return in_array($ip, $whitelist);
    }

    /**
     * Check IP blacklist
     */
    public function isIpBlacklisted(string $ip): bool
    {
        $blacklist = config('security.ip_blacklist', []);
        return in_array($ip, $blacklist);
    }

    /**
     * Get security recommendations
     */
    public function getSecurityRecommendations(User $user): array
    {
        $recommendations = [];

        // Check password age
        $passwordAge = $user->password_changed_at ? $user->password_changed_at->diffInDays(now()) : 365;
        if ($passwordAge > 90) {
            $recommendations[] = [
                'type' => 'password_age',
                'priority' => 'high',
                'message' => 'Your password is over 90 days old. Consider changing it.',
                'action' => 'change_password'
            ];
        }

        // Check two-factor authentication
        if (!$user->two_factor_enabled) {
            $recommendations[] = [
                'type' => 'two_factor',
                'priority' => 'medium',
                'message' => 'Enable two-factor authentication for better security.',
                'action' => 'enable_2fa'
            ];
        }

        // Check email verification
        if (!$user->email_verified_at) {
            $recommendations[] = [
                'type' => 'email_verification',
                'priority' => 'medium',
                'message' => 'Please verify your email address.',
                'action' => 'verify_email'
            ];
        }

        return $recommendations;
    }

    /**
     * Audit security settings
     */
    public function auditSecuritySettings(): array
    {
        return [
            'password_policy' => [
                'min_length' => 8,
                'require_uppercase' => true,
                'require_lowercase' => true,
                'require_numbers' => true,
                'require_symbols' => true,
                'max_age_days' => 90
            ],
            'session_security' => [
                'timeout_minutes' => 120,
                'regenerate_on_login' => true,
                'secure_cookies' => true,
                'http_only_cookies' => true
            ],
            'rate_limiting' => [
                'login_attempts' => 5,
                'api_requests_per_minute' => 60,
                'password_reset_attempts' => 3
            ],
            'encryption' => [
                'data_at_rest' => true,
                'data_in_transit' => true,
                'sensitive_fields_encrypted' => true
            ]
        ];
    }
}
