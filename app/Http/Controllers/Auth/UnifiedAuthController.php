<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\PasswordUpdateRequest;
use App\Models\User;
use App\Services\UserManagementService;
use App\Services\RoleBasedAccessControlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Mail\PasswordResetEmail;

class UnifiedAuthController extends Controller
{
    protected UserManagementService $userService;
    protected RoleBasedAccessControlService $rbacService;

    public function __construct(
        UserManagementService $userService,
        RoleBasedAccessControlService $rbacService
    ) {
        $this->userService = $userService;
        $this->rbacService = $rbacService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        
        // Update last login
        $user->update(['last_login_at' => now()]);

        // Clear permission cache
        $this->rbacService->clearUserCache($user);

        // Log successful login
        Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Redirect based on role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        // Check if registration is enabled
        if (!config('app.allow_registration', false)) {
            abort(403, 'Registration is currently disabled');
        }

        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(RegisterRequest $request)
    {
        try {
            $userData = $request->validated();
            $userData['role'] = 'tenant'; // Default role for self-registration
            $userData['email_verified_at'] = now(); // Auto-verify for now

            $user = $this->userService->createUser($userData);

            // Fire registered event
            event(new Registered($user));

            // Log in the user
            Auth::login($user);

            // Send welcome email
            Mail::to($user->email)->send(new WelcomeEmail($user));

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Registration successful! Welcome to Foxes Rentals.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ]);

            return back()->withErrors([
                'email' => 'Registration failed. Please try again.'
            ]);
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show password reset request form
     */
    public function showPasswordResetForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle password reset request
     */
    public function sendPasswordResetLink(PasswordResetRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            Log::info('Password reset link sent', [
                'email' => $request->email,
                'ip_address' => $request->ip()
            ]);

            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Show password reset form
     */
    public function showPasswordResetFormWithToken(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(PasswordUpdateRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'password_changed_at' => now(),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Log::info('Password reset successful', [
                'email' => $request->email,
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Show email verification notice
     */
    public function showEmailVerificationNotice()
    {
        return view('auth.verify-email');
    }

    /**
     * Send email verification notification
     */
    public function sendEmailVerificationNotification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Handle email verification
     */
    public function verifyEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($request->user()));
        }

        return redirect()->intended(route('dashboard'))
            ->with('status', 'email-verified');
    }

    /**
     * Show password confirmation form
     */
    public function showPasswordConfirmationForm()
    {
        return view('auth.confirm-password');
    }

    /**
     * Handle password confirmation
     */
    public function confirmPassword(Request $request)
    {
        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended();
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return redirect()->route('admin.home');
        }

        if ($user->hasRole('manager') || $user->hasRole('agent')) {
            return redirect()->route('admin.home');
        }

        if ($user->hasRole('landlord')) {
            return redirect()->route('landlord.dashboard');
        }

        if ($user->hasRole('tenant')) {
            return redirect()->route('tenant.dashboard');
        }

        // Default redirect
        return redirect()->route('dashboard');
    }

    /**
     * Check if user can access route
     */
    public function checkRouteAccess(Request $request, string $route)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $accessibleRoutes = $this->rbacService->getAccessibleRoutes($user);
        
        if (!in_array($route, $accessibleRoutes)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return true;
    }

    /**
     * Get user session info
     */
    public function getSessionInfo(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['authenticated' => false]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'last_login' => $user->last_login_at,
            ],
            'session' => [
                'id' => $request->session()->getId(),
                'lifetime' => config('session.lifetime'),
            ]
        ]);
    }

    /**
     * Refresh user session
     */
    public function refreshSession(Request $request)
    {
        $request->session()->regenerate();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Session refreshed successfully'
        ]);
    }

    /**
     * Get login attempts for IP
     */
    public function getLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);
        $attempts = RateLimiter::attempts($key);
        $remaining = RateLimiter::remaining($key, 5);
        
        return response()->json([
            'attempts' => $attempts,
            'remaining' => $remaining,
            'reset_time' => RateLimiter::availableIn($key)
        ]);
    }

    /**
     * Generate throttle key for rate limiting
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }
}

