<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EnhancedSettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct(EnhancedSettingsService $settingsService)
    {
        $this->middleware('permission:manage_settings');
        $this->settingsService = $settingsService;
    }

    /**
     * Display the main settings dashboard
     */
    public function index()
    {
        try {
            $categories = $this->settingsService->getAllSettings();
            $systemHealth = $this->settingsService->getSystemHealth();
            $stats = $this->getSystemStats();
            
            return view('admin.settings.modern-dashboard', compact('categories', 'systemHealth', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading settings dashboard: ' . $e->getMessage());
            return view('admin.settings.modern-dashboard')->with('error', 'Failed to load settings dashboard.');
        }
    }

    /**
     * Update a single setting
     */
    public function updateSetting(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->settingsService->setSetting(
                $request->key,
                $request->value,
                auth()->id()
            );

            Log::info("Setting updated: {$request->key} by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating setting {$request->key}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update multiple settings at once
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $settingsArray = [];
            foreach ($request->settings as $setting) {
                $settingsArray[$setting['key']] = $setting['value'];
            }

            $this->settingsService->setMultipleSettings(
                $settingsArray,
                auth()->id()
            );

            Log::info("Bulk settings update by user " . auth()->id() . ": " . implode(', ', array_keys($settingsArray)));

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Error in bulk settings update: " . $e->getMessage());
            
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
        $validator = Validator::make($request->all(), [
            'key' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $value = $this->settingsService->getSetting($request->key);
            
            return response()->json([
                'success' => true,
                'value' => $value
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
    public function getHistory(Request $request): JsonResponse
    {
        try {
            $history = $this->settingsService->getSettingsHistory(
                $request->get('setting_key'),
                $request->get('user_id'),
                $request->get('days', 30)
            );

            return response()->json([
                'success' => true,
                'history' => $history
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
            
            Log::info("Settings cache cleared by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Settings cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Error clearing settings cache: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Export settings
     */
    public function exportSettings(): JsonResponse
    {
        try {
            $settings = $this->settingsService->exportSettings();
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Import settings
     */
    public function importSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->settingsService->importSettings(
                $request->settings,
                auth()->id()
            );

            Log::info("Settings imported by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Settings imported successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Error importing settings: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Legacy methods for backward compatibility
    public function appearance()
    {
        return view('admin.settings.appearance');
    }

    public function house_types()
    {
        return view('admin.settings.house_types');
    }

    public function property_types()
    {
        return view('admin.settings.property_types');
    }

    public function payment_methods()
    {
        return view('admin.settings.payment_methods');
    }

    public function company_settings()
    {
        return view('admin.settings.company_settings');
    }

    public function expense_types()
    {
        return view('admin.settings.expense_types');
    }

    /**
     * Get system statistics
     */
    private function getSystemStats(): array
    {
        try {
            return [
                'users' => \App\Models\User::count(),
                'new_users_today' => \App\Models\User::whereDate('created_at', today())->count(),
                'properties' => \App\Models\RentalProperty::count() + (\App\Models\SaleProperty::count() ?? 0),
                'vacant_properties' => \App\Models\RentalProperty::where('status', 'vacant')->count(),
                'api_keys' => \App\Models\ApiKey::count(),
                'expired_keys' => \App\Models\ApiKey::where('expires_at', '<', now())->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting system stats: ' . $e->getMessage());
            return [
                'users' => 0,
                'new_users_today' => 0,
                'properties' => 0,
                'vacant_properties' => 0,
                'api_keys' => 0,
                'expired_keys' => 0,
            ];
        }
    }
}
