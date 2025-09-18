<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingsCategory;
use App\Models\SettingsGroup;
use App\Models\SettingsItem;
use App\Services\EnhancedSettingsService;
use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(EnhancedSettingsService $settingsService)
    {
        $this->middleware('permission:manage user settings');
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        // Get all user-related settings categories
        $userCategories = SettingsCategory::where('slug', 'user-management')
            ->with(['groups.items' => function($query) {
                $query->orderBy('order_index');
            }])
            ->get();

        return view('admin.settings.users.index', compact('userCategories'));
    }

    public function roles()
    {
        $roleGroup = SettingsGroup::where('slug', 'role-management')
            ->with(['items' => function($query) {
                $query->orderBy('order_index');
            }])
            ->first();

        $categories = SettingsCategory::where('slug', 'user-management')->get();

        return view('admin.settings.users.roles', compact('roleGroup', 'categories'));
    }

    public function permissions()
    {
        $permissionGroup = SettingsGroup::where('slug', 'permission-management')
            ->with(['items' => function($query) {
                $query->orderBy('order_index');
            }])
            ->first();

        $categories = SettingsCategory::where('slug', 'user-management')->get();

        return view('admin.settings.users.permissions', compact('permissionGroup', 'categories'));
    }

    public function profiles()
    {
        $profileGroup = SettingsGroup::where('slug', 'user-profile-settings')
            ->with(['items' => function($query) {
                $query->orderBy('order_index');
            }])
            ->first();

        $categories = SettingsCategory::where('slug', 'user-management')->get();

        return view('admin.settings.users.profiles', compact('profileGroup', 'categories'));
    }

    public function security()
    {
        $securityGroup = SettingsGroup::where('slug', 'account-security')
            ->with(['items' => function($query) {
                $query->orderBy('order_index');
            }])
            ->first();

        $categories = SettingsCategory::where('slug', 'user-management')->get();

        return view('admin.settings.users.security', compact('securityGroup', 'categories'));
    }

    public function registration()
    {
        $registrationGroup = SettingsGroup::where('slug', 'user-registration')
            ->with(['items' => function($query) {
                $query->orderBy('order_index');
            }])
            ->first();

        $categories = SettingsCategory::where('slug', 'user-management')->get();

        return view('admin.settings.users.registration', compact('registrationGroup', 'categories'));
    }

    public function updateSetting(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable',
        ]);

        try {
            $this->settingsService->setSetting($request->input('key'), $request->input('value'));
            return response()->json(['message' => 'Setting updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update setting.'], 500);
        }
    }
}
