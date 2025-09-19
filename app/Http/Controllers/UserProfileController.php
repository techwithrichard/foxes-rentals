<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdatePreferencesRequest;
use App\Services\UserManagementService;
use App\Services\RoleBasedAccessControlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    protected UserManagementService $userService;
    protected RoleBasedAccessControlService $rbacService;

    public function __construct(
        UserManagementService $userService,
        RoleBasedAccessControlService $rbacService
    ) {
        $this->middleware('auth');
        $this->userService = $userService;
        $this->rbacService = $rbacService;
    }

    /**
     * Show user profile
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $user->load(['roles', 'permissions']);
        
        $activitySummary = $this->userService->getUserActivitySummary($user->id);
        $userPermissions = $this->rbacService->getUserPermissions($user);
        $userRoles = $this->rbacService->getUserRoles($user);

        return view('profile.show', compact('user', 'activitySummary', 'userPermissions', 'userRoles'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $data['profile_picture'] = $this->handleProfilePictureUpload($request->file('profile_picture'), $user);
            }

            // Update user information
            $updatedUser = $this->userService->updateUser($user->id, $data);

            Log::info('User profile updated', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($data)
            ]);

            return redirect()->route('profile.show')
                ->with('success', 'Profile updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update user profile', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Failed to update profile. Please try again.'
            ]);
        }
    }

    /**
     * Show change password form
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Change user password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = Auth::user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'The current password is incorrect.'
                ]);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password),
                'password_changed_at' => now()
            ]);

            Log::info('User password changed', [
                'user_id' => $user->id,
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('profile.show')
                ->with('success', 'Password changed successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to change password', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Failed to change password. Please try again.'
            ]);
        }
    }

    /**
     * Show user preferences
     */
    public function showPreferences()
    {
        $user = Auth::user();
        return view('profile.preferences', compact('user'));
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(UpdatePreferencesRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            $user->update($data);

            Log::info('User preferences updated', [
                'user_id' => $user->id,
                'preferences' => $data
            ]);

            return redirect()->route('profile.preferences')
                ->with('success', 'Preferences updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update preferences', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Failed to update preferences. Please try again.'
            ]);
        }
    }

    /**
     * Show user activity log
     */
    public function showActivityLog(Request $request)
    {
        $user = Auth::user();
        
        // Get user activity (this would be implemented with an activity log model)
        $activities = $this->getUserActivities($user->id, $request->get('page', 1));

        return view('profile.activity-log', compact('user', 'activities'));
    }

    /**
     * Show user security settings
     */
    public function showSecuritySettings()
    {
        $user = Auth::user();
        
        return view('profile.security-settings', compact('user'));
    }

    /**
     * Update security settings
     */
    public function updateSecuritySettings(Request $request)
    {
        $request->validate([
            'two_factor_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);

        try {
            $user = Auth::user();
            
            $user->update([
                'two_factor_enabled' => $request->boolean('two_factor_enabled'),
                'email_notifications' => $request->boolean('email_notifications'),
                'sms_notifications' => $request->boolean('sms_notifications'),
            ]);

            Log::info('Security settings updated', [
                'user_id' => $user->id,
                'settings' => $request->only(['two_factor_enabled', 'email_notifications', 'sms_notifications'])
            ]);

            return redirect()->route('profile.security-settings')
                ->with('success', 'Security settings updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update security settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Failed to update security settings. Please try again.'
            ]);
        }
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'confirmation' => 'required|in:DELETE',
        ]);

        try {
            $user = Auth::user();

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors([
                    'password' => 'The password is incorrect.'
                ]);
            }

            // Log out user
            Auth::logout();

            // Delete user account
            $this->userService->forceDeleteUser($user->id);

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('User account deleted', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);

            return redirect()->route('login')
                ->with('success', 'Your account has been deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete user account', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'error' => 'Failed to delete account. Please try again.'
            ]);
        }
    }

    /**
     * Handle profile picture upload
     */
    protected function handleProfilePictureUpload($file, $user)
    {
        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Generate unique filename
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $path = $file->storeAs('profile-pictures', $filename, 'public');

        return $path;
    }

    /**
     * Get user activities (placeholder implementation)
     */
    protected function getUserActivities($userId, $page = 1)
    {
        // This would be implemented with an actual activity log model
        return collect([
            [
                'id' => 1,
                'action' => 'profile_updated',
                'description' => 'Updated profile information',
                'created_at' => now()->subHours(2),
            ],
            [
                'id' => 2,
                'action' => 'password_changed',
                'description' => 'Changed password',
                'created_at' => now()->subDays(1),
            ],
            [
                'id' => 3,
                'action' => 'login',
                'description' => 'Logged in',
                'created_at' => now()->subDays(2),
            ],
        ]);
    }

    /**
     * Download user data
     */
    public function downloadUserData()
    {
        $user = Auth::user();
        
        $userData = [
            'profile' => $user->toArray(),
            'roles' => $user->roles->toArray(),
            'permissions' => $user->getAllPermissions()->toArray(),
            'activities' => $this->getUserActivities($user->id),
            'exported_at' => now()->toISOString(),
        ];

        $filename = 'user_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($userData)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }
}

