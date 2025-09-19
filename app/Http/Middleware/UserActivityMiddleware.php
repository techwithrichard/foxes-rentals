<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\UserActivityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserActivityMiddleware
{
    protected UserActivityService $activityService;

    public function __construct(UserActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log activity for authenticated users
        if (Auth::check() && $this->shouldLogActivity($request)) {
            try {
                $this->logUserActivity($request, $response);
            } catch (\Exception $e) {
                Log::error('Failed to log user activity', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                    'route' => $request->route()->getName(),
                ]);
            }
        }

        return $response;
    }

    /**
     * Determine if activity should be logged
     */
    protected function shouldLogActivity(Request $request): bool
    {
        // Skip logging for certain routes
        $skipRoutes = [
            'login',
            'logout',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'password.confirm',
        ];

        $routeName = $request->route()->getName();
        
        if (in_array($routeName, $skipRoutes)) {
            return false;
        }

        // Skip logging for AJAX requests unless they're important
        if ($request->ajax() && !$this->isImportantAjaxRequest($request)) {
            return false;
        }

        // Skip logging for asset requests
        if ($request->is('css/*') || $request->is('js/*') || $request->is('images/*')) {
            return false;
        }

        return true;
    }

    /**
     * Check if AJAX request is important enough to log
     */
    protected function isImportantAjaxRequest(Request $request): bool
    {
        $importantRoutes = [
            'admin.users.store',
            'admin.users.update',
            'admin.users.destroy',
            'admin.properties.store',
            'admin.properties.update',
            'admin.properties.destroy',
            'admin.leases.store',
            'admin.leases.update',
            'admin.leases.destroy',
        ];

        return in_array($request->route()->getName(), $importantRoutes);
    }

    /**
     * Log user activity
     */
    protected function logUserActivity(Request $request, $response): void
    {
        $user = Auth::user();
        $routeName = $request->route()->getName();
        $method = $request->method();
        $statusCode = $response->getStatusCode();

        // Determine action based on route and method
        $action = $this->determineAction($routeName, $method);
        
        if (!$action) {
            return;
        }

        // Create description
        $description = $this->createDescription($routeName, $method, $statusCode);

        // Prepare metadata
        $metadata = [
            'route' => $routeName,
            'method' => $method,
            'status_code' => $statusCode,
            'user_agent' => $request->userAgent(),
        ];

        // Add request data for important actions
        if ($this->isImportantAction($action)) {
            $metadata['request_data'] = $this->getRequestData($request);
        }

        // Log the activity
        $this->activityService->logActivity(
            $user,
            $action,
            $description,
            $request,
            $metadata
        );
    }

    /**
     * Determine action from route and method
     */
    protected function determineAction(string $routeName, string $method): ?string
    {
        // Login actions
        if (str_contains($routeName, 'login')) {
            return 'login';
        }

        // Logout actions
        if (str_contains($routeName, 'logout')) {
            return 'logout';
        }

        // Profile actions
        if (str_contains($routeName, 'profile')) {
            return match ($method) {
                'GET' => 'profile_viewed',
                'PUT', 'PATCH' => 'profile_updated',
                default => null,
            };
        }

        // User management actions
        if (str_contains($routeName, 'users')) {
            return match ($method) {
                'GET' => 'users_viewed',
                'POST' => 'user_created',
                'PUT', 'PATCH' => 'user_updated',
                'DELETE' => 'user_deleted',
                default => null,
            };
        }

        // Property management actions
        if (str_contains($routeName, 'properties')) {
            return match ($method) {
                'GET' => 'properties_viewed',
                'POST' => 'property_created',
                'PUT', 'PATCH' => 'property_updated',
                'DELETE' => 'property_deleted',
                default => null,
            };
        }

        // Lease management actions
        if (str_contains($routeName, 'leases')) {
            return match ($method) {
                'GET' => 'leases_viewed',
                'POST' => 'lease_created',
                'PUT', 'PATCH' => 'lease_updated',
                'DELETE' => 'lease_deleted',
                default => null,
            };
        }

        // Payment actions
        if (str_contains($routeName, 'payments')) {
            return match ($method) {
                'GET' => 'payments_viewed',
                'POST' => 'payment_created',
                'PUT', 'PATCH' => 'payment_updated',
                'DELETE' => 'payment_deleted',
                default => null,
            };
        }

        // Settings actions
        if (str_contains($routeName, 'settings')) {
            return match ($method) {
                'GET' => 'settings_viewed',
                'PUT', 'PATCH' => 'settings_updated',
                default => null,
            };
        }

        // Dashboard actions
        if (str_contains($routeName, 'dashboard')) {
            return 'dashboard_viewed';
        }

        // Reports actions
        if (str_contains($routeName, 'reports')) {
            return 'reports_viewed';
        }

        return null;
    }

    /**
     * Create activity description
     */
    protected function createDescription(string $routeName, string $method, int $statusCode): string
    {
        $action = $this->determineAction($routeName, $method);
        
        if (!$action) {
            return "Accessed {$routeName}";
        }

        $descriptions = [
            'login' => 'User logged in',
            'logout' => 'User logged out',
            'profile_viewed' => 'Viewed profile',
            'profile_updated' => 'Updated profile',
            'users_viewed' => 'Viewed users list',
            'user_created' => 'Created new user',
            'user_updated' => 'Updated user information',
            'user_deleted' => 'Deleted user',
            'properties_viewed' => 'Viewed properties list',
            'property_created' => 'Created new property',
            'property_updated' => 'Updated property information',
            'property_deleted' => 'Deleted property',
            'leases_viewed' => 'Viewed leases list',
            'lease_created' => 'Created new lease',
            'lease_updated' => 'Updated lease information',
            'lease_deleted' => 'Deleted lease',
            'payments_viewed' => 'Viewed payments list',
            'payment_created' => 'Created new payment',
            'payment_updated' => 'Updated payment information',
            'payment_deleted' => 'Deleted payment',
            'settings_viewed' => 'Viewed settings',
            'settings_updated' => 'Updated settings',
            'dashboard_viewed' => 'Viewed dashboard',
            'reports_viewed' => 'Viewed reports',
        ];

        return $descriptions[$action] ?? "Performed {$action}";
    }

    /**
     * Check if action is important enough to log request data
     */
    protected function isImportantAction(string $action): bool
    {
        $importantActions = [
            'user_created',
            'user_updated',
            'user_deleted',
            'property_created',
            'property_updated',
            'property_deleted',
            'lease_created',
            'lease_updated',
            'lease_deleted',
            'payment_created',
            'payment_updated',
            'payment_deleted',
            'settings_updated',
        ];

        return in_array($action, $importantActions);
    }

    /**
     * Get sanitized request data
     */
    protected function getRequestData(Request $request): array
    {
        $data = $request->all();
        
        // Remove sensitive fields
        $sensitiveFields = ['password', 'password_confirmation', 'current_password', 'token'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return $data;
    }
}

