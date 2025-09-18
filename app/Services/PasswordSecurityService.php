<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordSecurityService
{
    /**
     * Password complexity requirements
     */
    protected array $complexityRules = [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'max_length' => 128,
    ];

    /**
     * Password history requirements
     */
    protected int $passwordHistoryCount = 5;

    /**
     * Password expiry settings
     */
    protected int $passwordExpiryDays = 90;

    /**
     * Check password complexity
     */
    public function checkPasswordComplexity(string $password): array
    {
        $errors = [];

        if (strlen($password) < $this->complexityRules['min_length']) {
            $errors[] = "Password must be at least {$this->complexityRules['min_length']} characters long.";
        }

        if (strlen($password) > $this->complexityRules['max_length']) {
            $errors[] = "Password must not exceed {$this->complexityRules['max_length']} characters.";
        }

        if ($this->complexityRules['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }

        if ($this->complexityRules['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }

        if ($this->complexityRules['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }

        if ($this->complexityRules['require_symbols'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }

        return $errors;
    }

    /**
     * Check if password is in history
     */
    public function isPasswordInHistory(User $user, string $password): bool
    {
        $recentPasswords = PasswordHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($this->passwordHistoryCount)
            ->get();

        foreach ($recentPasswords as $passwordRecord) {
            if (Hash::check($password, $passwordRecord->password_hash)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate password against all security policies
     */
    public function validatePassword(User $user, string $password): array
    {
        $errors = [];

        // Check complexity
        $complexityErrors = $this->checkPasswordComplexity($password);
        $errors = array_merge($errors, $complexityErrors);

        // Check history
        if ($this->isPasswordInHistory($user, $password)) {
            $errors[] = 'Password has been used recently. Please choose a different password.';
        }

        // Check common passwords
        if ($this->isCommonPassword($password)) {
            $errors[] = 'Password is too common. Please choose a more unique password.';
        }

        return $errors;
    }

    /**
     * Check if password is common
     */
    public function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            'password', '123456', '123456789', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey',
            '1234567890', 'password1', 'qwerty123', 'dragon', 'master',
            'hello', 'freedom', 'whatever', 'qazwsx', 'trustno1',
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * Update user password with security checks
     */
    public function updatePassword(User $user, string $newPassword, bool $forceUpdate = false): bool
    {
        // Validate password unless forced
        if (!$forceUpdate) {
            $errors = $this->validatePassword($user, $newPassword);
            if (!empty($errors)) {
                throw new \Exception(implode(' ', $errors));
            }
        }

        // Hash new password
        $hashedPassword = Hash::make($newPassword);

        // Update user password
        $user->update([
            'password' => $hashedPassword,
            'password_changed_at' => now(),
        ]);

        // Add to password history
        $this->addToPasswordHistory($user, $hashedPassword);

        // Clear password-related cache
        $this->clearPasswordCache($user);

        Log::info('User password updated', [
            'user_id' => $user->id,
            'password_changed_at' => now(),
        ]);

        return true;
    }

    /**
     * Add password to history
     */
    protected function addToPasswordHistory(User $user, string $hashedPassword): void
    {
        PasswordHistory::create([
            'user_id' => $user->id,
            'password_hash' => $hashedPassword,
            'created_at' => now(),
        ]);

        // Clean up old password history
        $this->cleanupPasswordHistory($user);
    }

    /**
     * Clean up old password history
     */
    protected function cleanupPasswordHistory(User $user): void
    {
        $oldPasswords = PasswordHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->skip($this->passwordHistoryCount)
            ->get();

        foreach ($oldPasswords as $oldPassword) {
            $oldPassword->delete();
        }
    }

    /**
     * Check if password is expired
     */
    public function isPasswordExpired(User $user): bool
    {
        if (!$user->password_changed_at) {
            return true; // Never changed, consider expired
        }

        return $user->password_changed_at->addDays($this->passwordExpiryDays) < now();
    }

    /**
     * Get days until password expires
     */
    public function getDaysUntilPasswordExpiry(User $user): int
    {
        if (!$user->password_changed_at) {
            return 0; // Expired
        }

        $expiryDate = $user->password_changed_at->addDays($this->passwordExpiryDays);
        $daysUntilExpiry = now()->diffInDays($expiryDate, false);

        return max(0, $daysUntilExpiry);
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
        $allChars = $uppercase . $lowercase . $numbers . $symbols;

        // Ensure at least one character from each required set
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill the rest with random characters
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Get password strength score
     */
    public function getPasswordStrength(string $password): array
    {
        $score = 0;
        $feedback = [];

        // Length check
        if (strlen($password) >= 8) {
            $score += 1;
        } else {
            $feedback[] = 'Use at least 8 characters';
        }

        // Character variety checks
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Add lowercase letters';
        }

        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Add uppercase letters';
        }

        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Add numbers';
        }

        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Add special characters';
        }

        // Length bonus
        if (strlen($password) >= 12) {
            $score += 1;
        }

        // Uniqueness bonus
        if (!$this->isCommonPassword($password)) {
            $score += 1;
        }

        $strength = match (true) {
            $score <= 2 => 'weak',
            $score <= 4 => 'fair',
            $score <= 6 => 'good',
            default => 'strong',
        };

        return [
            'score' => $score,
            'strength' => $strength,
            'feedback' => $feedback,
        ];
    }

    /**
     * Clear password-related cache
     */
    protected function clearPasswordCache(User $user): void
    {
        Cache::forget("password_expiry_{$user->id}");
        Cache::forget("password_history_{$user->id}");
    }

    /**
     * Get users with expired passwords
     */
    public function getUsersWithExpiredPasswords(): \Illuminate\Database\Eloquent\Collection
    {
        return User::where(function ($query) {
            $query->whereNull('password_changed_at')
                  ->orWhere('password_changed_at', '<', now()->subDays($this->passwordExpiryDays));
        })->get();
    }

    /**
     * Get users with passwords expiring soon
     */
    public function getUsersWithPasswordsExpiringSoon(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        $expiryDate = now()->addDays($days);
        $expiryStart = now()->subDays($this->passwordExpiryDays);

        return User::where('password_changed_at', '>=', $expiryStart)
            ->where('password_changed_at', '<=', $expiryDate)
            ->get();
    }

    /**
     * Force password reset for user
     */
    public function forcePasswordReset(User $user): string
    {
        $newPassword = $this->generateSecurePassword();
        $this->updatePassword($user, $newPassword, true);

        Log::info('Password reset forced', [
            'user_id' => $user->id,
            'reset_by' => auth()->id(),
        ]);

        return $newPassword;
    }

    /**
     * Get password security statistics
     */
    public function getPasswordSecurityStatistics(): array
    {
        $totalUsers = User::count();
        $expiredPasswords = $this->getUsersWithExpiredPasswords()->count();
        $expiringSoon = $this->getUsersWithPasswordsExpiringSoon()->count();
        $neverChanged = User::whereNull('password_changed_at')->count();

        return [
            'total_users' => $totalUsers,
            'expired_passwords' => $expiredPasswords,
            'expiring_soon' => $expiringSoon,
            'never_changed' => $neverChanged,
            'compliant_users' => $totalUsers - $expiredPasswords - $neverChanged,
        ];
    }

    /**
     * Update password complexity rules
     */
    public function updateComplexityRules(array $rules): void
    {
        $this->complexityRules = array_merge($this->complexityRules, $rules);
    }

    /**
     * Update password history count
     */
    public function updatePasswordHistoryCount(int $count): void
    {
        $this->passwordHistoryCount = $count;
    }

    /**
     * Update password expiry days
     */
    public function updatePasswordExpiryDays(int $days): void
    {
        $this->passwordExpiryDays = $days;
    }
}
