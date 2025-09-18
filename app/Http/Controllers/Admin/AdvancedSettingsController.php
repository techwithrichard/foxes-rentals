<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingsCategory;
use App\Models\SettingsGroup;
use App\Models\SettingsItem;
use App\Services\EnhancedSettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdvancedSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(EnhancedSettingsService $settingsService)
    {
        $this->middleware('permission:view settings');
        $this->settingsService = $settingsService;
    }

    /**
     * Display the advanced settings dashboard
     */
    public function index()
    {
        $categories = $this->settingsService->getAllSettings();
        
        return view('admin.settings.advanced.index', compact('categories'));
    }

    /**
     * Display settings for a specific category
     */
    public function category($categorySlug)
    {
        $category = $this->settingsService->getSettingsByCategory($categorySlug);
        
        if (!$category) {
            abort(404, 'Settings category not found');
        }

        return view('admin.settings.advanced.category', compact('category'));
    }

    /**
     * Update a single setting
     */
    public function updateSetting(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required'
        ]);

        try {
            $this->settingsService->setSetting(
                $request->key,
                $request->value,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update multiple settings
     */
    public function updateMultipleSettings(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array'
        ]);

        try {
            $this->settingsService->setMultipleSettings(
                $request->settings,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get settings history
     */
    public function getHistory(Request $request)
    {
        $history = $this->settingsService->getSettingsHistory(
            $request->get('setting_key'),
            $request->get('user_id'),
            $request->get('days', 30)
        );

        return response()->json($history);
    }

    /**
     * Export settings
     */
    public function exportSettings()
    {
        $settings = $this->settingsService->exportSettings();
        
        return response()->json($settings);
    }

    /**
     * Import settings
     */
    public function importSettings(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array'
        ]);

        try {
            $this->settingsService->importSettings(
                $request->settings,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Settings imported successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Clear settings cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->settingsService->clearAllCache();

            return response()->json([
                'success' => true,
                'message' => 'Settings cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get a specific setting value
     */
    public function getSetting(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string'
        ]);

        $value = $this->settingsService->getSetting($request->key);

        return response()->json([
            'success' => true,
            'value' => $value
        ]);
    }

    /**
     * Create a new settings category
     */
    public function createCategory(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:settings_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'order_index' => 'nullable|integer|min:0'
        ]);

        try {
            $category = $this->settingsService->createCategory($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create a new settings group
     */
    public function createGroup(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|uuid|exists:settings_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer|min:0'
        ]);

        try {
            $group = $this->settingsService->createGroup($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'group' => $group
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create a new settings item
     */
    public function createSetting(Request $request): JsonResponse
    {
        $request->validate([
            'group_id' => 'required|uuid|exists:settings_groups,id',
            'key' => 'required|string|max:255|unique:settings_items,key',
            'value' => 'nullable',
            'type' => 'required|in:text,number,boolean,select,multiselect,file,json,email,url,password',
            'description' => 'nullable|string',
            'is_encrypted' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'default_value' => 'nullable',
            'options' => 'nullable|array',
            'placeholder' => 'nullable|string|max:255',
            'validation_rules' => 'nullable|array'
        ]);

        try {
            $setting = $this->settingsService->createSetting($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Setting created successfully',
                'setting' => $setting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}