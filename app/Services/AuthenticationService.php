<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthenticationService
{
    /**
     * Authenticate user with email and password
     */
    public function authenticate(array $credentials, bool $remember = false): array
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$email || !$password) {
            return [
                'success' => false,
                'message' => 'Email and password are required'
            ];
        }

        // Check rate limiting
        if ($this->isRateLimited($email)) {
            return [
                'success' => false,
                'message' => 'Too many login attempts. Please try again later.'
            ];
        }

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->recordFailedAttempt($email);
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        // Check if user is active
        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => 'Account is deactivated. Please contact support.'
            ];
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            $this->recordFailedAttempt($email);
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        // Clear failed attempts
        $this->clearFailedAttempts($email);

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            Log::info('User authenticated successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => request()->ip()
            ]);

            return [
                'success' => true,
                'user' => $user->load('roles'),
                'message' => 'Login successful'
            ];
        }

        return [
            'success' => false,
            'message' => 'Authentication failed'
        ];
    }

    /**
     * Register a new user
     */
    public function register(array $userData, string $role = 'tenant'): array
    {
        try {
            // Validate user data
            $userService = app(UserService::class);
            $errors = $userService->validateUserData($userData);

            if (!empty($errors)) {
                return [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ];
            }

            // Create user
            $user = $userService->createUser($userData, $role);

            // Auto-login if requested
            if (isset($userData['auto_login']) && $userData['auto_login']) {
                Auth::login($user);
            }

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $role
            ]);

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Registration successful'
            ];

        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'user_data' => $userData
            ]);

            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Logout user
     */
    public function logout(): array
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        Auth::logout();

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        // Verify current password
        if (!Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect'
            ];
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => 'New password must be at least 8 characters'
            ];
        }

        // Update password
        $user->update(['password' => Hash::make($newPassword)]);

        Log::info('User password changed', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return [
            'success' => true,
            'message' => 'Password changed successfully'
        ];
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found'
            ];
        }

        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => 'Account is deactivated'
            ];
        }

        // Generate reset token
        $userService = app(UserService::class);
        $token = $userService->generatePasswordResetToken($user);

        // TODO: Send reset email
        // Mail::to($user->email)->send(new PasswordResetMail($token));

        Log::info('Password reset requested', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return [
            'success' => true,
            'message' => 'Password reset instructions sent to your email',
            'token' => $token // Remove this in production
        ];
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        $user = User::where('password_reset_token', $token)
            ->where('password_reset_expires_at', '>', now())
            ->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or expired reset token'
            ];
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => 'Password must be at least 8 characters'
            ];
        }

        // Reset password
        $userService = app(UserService::class);
        $userService->resetPassword($user, $newPassword);

        Log::info('Password reset completed', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return [
            'success' => true,
            'message' => 'Password reset successfully'
        ];
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token): array
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid verification token'
            ];
        }

        if ($user->email_verified_at) {
            return [
                'success' => false,
                'message' => 'Email already verified'
            ];
        }

        // Verify email
        $userService = app(UserService::class);
        $userService->verifyEmail($user, $token);

        Log::info('Email verified', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return [
            'success' => true,
            'message' => 'Email verified successfully',
            'user' => $user->fresh()
        ];
    }

    /**
     * Resend email verification
     */
    public function resendEmailVerification(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found'
            ];
        }

        if ($user->email_verified_at) {
            return [
                'success' => false,
                'message' => 'Email already verified'
            ];
        }

        // Generate new verification token
        $user->update(['email_verification_token' => Str::random(60)]);

        // TODO: Send verification email
        // Mail::to($user->email)->send(new EmailVerificationMail($user->email_verification_token));

        Log::info('Email verification resent', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return [
            'success' => true,
            'message' => 'Verification email sent',
            'token' => $user->email_verification_token // Remove this in production
        ];
    }

    /**
     * Check if user is rate limited
     */
    protected function isRateLimited(string $email): bool
    {
        $key = 'login_attempts:' . $email;
        return RateLimiter::tooManyAttempts($key, 5);
    }

    /**
     * Record failed login attempt
     */
    protected function recordFailedAttempt(string $email): void
    {
        $key = 'login_attempts:' . $email;
        RateLimiter::hit($key, 300); // 5 minutes
    }

    /**
     * Clear failed login attempts
     */
    protected function clearFailedAttempts(string $email): void
    {
        $key = 'login_attempts:' . $email;
        RateLimiter::clear($key);
    }

    /**
     * Get current user
     */
    public function getCurrentUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Get user permissions
     */
    public function getUserPermissions(User $user): array
    {
        $permissions = $user->getAllPermissions();
        return $permissions->pluck('name')->toArray();
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(User $user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }

    /**
     * Check if user has role
     */
    public function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * Get user roles
     */
    public function getUserRoles(User $user): array
    {
        return $user->getRoleNames()->toArray();
    }

    /**
     * Get authentication statistics
     */
    public function getAuthenticationStatistics(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $recentLogins = User::where('last_login_at', '>=', now()->subDays(7))->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'verified_users' => $verifiedUsers,
            'recent_logins' => $recentLogins,
            'verification_rate' => $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 2) : 0
        ];
    }
}
