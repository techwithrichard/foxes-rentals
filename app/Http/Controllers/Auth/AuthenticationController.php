<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthenticationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    protected $authService;
    protected $userService;

    public function __construct(
        AuthenticationService $authService,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'remember' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->authenticate(
            $request->only(['email', 'password']),
            $request->boolean('remember')
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'user' => $result['user'],
                'token' => $result['user']->createToken('auth-token')->plainTextToken
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 401);
    }

    /**
     * Register new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:tenant,landlord,manager',
            'auto_login' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userData = $request->only([
            'first_name', 'last_name', 'email', 'phone', 'password'
        ]);

        $userData['name'] = $userData['first_name'] . ' ' . $userData['last_name'];
        $userData['auto_login'] = $request->boolean('auto_login');

        $result = $this->authService->register(
            $userData,
            $request->get('role', 'tenant')
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'user' => $result['user']
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'errors' => $result['errors'] ?? []
        ], 400);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout();

        // Revoke current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => $user->load(['roles', 'permissions'])
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->changePassword(
            $request->user(),
            $request->current_password,
            $request->new_password
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->requestPasswordReset($request->email);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->resetPassword(
            $request->token,
            $request->password
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->verifyEmail($request->token);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'user' => $result['user'] ?? null
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Resend email verification
     */
    public function resendEmailVerification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->authService->resendEmailVerification($request->email);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'token' => $token,
            'user' => $user->load(['roles', 'permissions'])
        ]);
    }

    /**
     * Get authentication statistics
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->authService->getAuthenticationStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Check authentication status
     */
    public function check(): JsonResponse
    {
        $isAuthenticated = $this->authService->isAuthenticated();
        $user = $isAuthenticated ? $this->authService->getCurrentUser() : null;

        return response()->json([
            'success' => true,
            'authenticated' => $isAuthenticated,
            'user' => $user ? $user->load(['roles', 'permissions']) : null
        ]);
    }
}
