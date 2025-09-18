<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RoleBasedAccessControlService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnhancedRoleMiddleware
{
    protected RoleBasedAccessControlService $rbacService;

    public function __construct(RoleBasedAccessControlService $rbacService)
    {
        $this->rbacService = $rbacService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $roles = null, string $permissions = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        // Check roles if specified
        if ($roles) {
            $requiredRoles = explode('|', $roles);
            
            if (!$this->rbacService->hasAnyRole($user, $requiredRoles)) {
                Log::warning('Access denied - insufficient role', [
                    'user_id' => $user->id,
                    'required_roles' => $requiredRoles,
                    'user_roles' => $user->roles->pluck('name')->toArray(),
                    'route' => $request->route()->getName(),
                    'ip' => $request->ip(),
                ]);

                return $this->handleAccessDenied($request);
            }
        }

        // Check permissions if specified
        if ($permissions) {
            $requiredPermissions = explode('|', $permissions);
            
            if (!$this->rbacService->hasAnyPermission($user, $requiredPermissions)) {
                Log::warning('Access denied - insufficient permission', [
                    'user_id' => $user->id,
                    'required_permissions' => $requiredPermissions,
                    'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                    'route' => $request->route()->getName(),
                    'ip' => $request->ip(),
                ]);

                return $this->handleAccessDenied($request);
            }
        }

        // Log successful access
        Log::info('Access granted', [
            'user_id' => $user->id,
            'route' => $request->route()->getName(),
            'ip' => $request->ip(),
        ]);

        return $next($request);
    }

    /**
     * Handle access denied
     */
    protected function handleAccessDenied(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Access denied. You do not have permission to access this resource.',
                'error' => 'FORBIDDEN'
            ], 403);
        }

        return redirect()->back()->with('error', 'Access denied. You do not have permission to access this resource.');
    }
}
