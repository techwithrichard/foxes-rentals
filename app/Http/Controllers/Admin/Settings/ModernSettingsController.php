<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Services\EnhancedSettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ModernSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(EnhancedSettingsService $settingsService)
    {
        $this->middleware('permission:manage_settings');
        $this->settingsService = $settingsService;
    }

    /**
     * Display the modern settings dashboard
     */
    public function index()
    {
        try {
            $stats = $this->getSystemStats();
            $systemHealth = $this->settingsService->getSystemHealth();
            
            return view('admin.settings.modern-dashboard', compact('stats', 'systemHealth'));
        } catch (\Exception $e) {
            Log::error('Error loading modern settings dashboard: ' . $e->getMessage());
            return view('admin.settings.modern-dashboard')->with('error', 'Failed to load settings dashboard.');
        }
    }

    /**
     * General Settings Page
     */
    public function general()
    {
        try {
            $settings = $this->settingsService->getSettingsByCategory('general');
            return view('admin.settings.pages.general', compact('settings'));
        } catch (\Exception $e) {
            Log::error('Error loading general settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load general settings.');
        }
    }

    /**
     * Company Settings Page
     */
    public function company()
    {
        try {
            $settings = $this->settingsService->getSettingsByCategory('company');
            return view('admin.settings.pages.company', compact('settings'));
        } catch (\Exception $e) {
            Log::error('Error loading company settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load company settings.');
        }
    }

    /**
     * Appearance Settings Page
     */
    public function appearance()
    {
        try {
            $settings = $this->settingsService->getSettingsByCategory('appearance');
            return view('admin.settings.pages.appearance', compact('settings'));
        } catch (\Exception $e) {
            Log::error('Error loading appearance settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load appearance settings.');
        }
    }

    /**
     * Property Types Settings Page
     */
    public function propertyTypes()
    {
        try {
            $propertyTypes = \App\Models\PropertyType::withCount(['rentalProperties', 'saleProperties'])->get();
            return view('admin.settings.pages.property-types', compact('propertyTypes'));
        } catch (\Exception $e) {
            Log::error('Error loading property types: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load property types.');
        }
    }

    /**
     * House Types Settings Page
     */
    public function houseTypes()
    {
        try {
            $houseTypes = \App\Models\HouseType::withCount('houses')->get();
            return view('admin.settings.pages.house-types', compact('houseTypes'));
        } catch (\Exception $e) {
            Log::error('Error loading house types: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load house types.');
        }
    }

    /**
     * Amenities Settings Page
     */
    public function amenities()
    {
        try {
            $amenities = \App\Models\PropertyAmenity::withCount('properties')->get();
            return view('admin.settings.pages.amenities', compact('amenities'));
        } catch (\Exception $e) {
            Log::error('Error loading amenities: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load amenities.');
        }
    }

    /**
     * Payment Methods Settings Page
     */
    public function paymentMethods()
    {
        try {
            $paymentMethods = \App\Models\PaymentMethod::withCount('payments')->get();
            return view('admin.settings.pages.payment-methods', compact('paymentMethods'));
        } catch (\Exception $e) {
            Log::error('Error loading payment methods: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load payment methods.');
        }
    }

    /**
     * Expense Types Settings Page
     */
    public function expenseTypes()
    {
        try {
            $expenseTypes = \App\Models\ExpenseType::withCount('expenses')->get();
            return view('admin.settings.pages.expense-types', compact('expenseTypes'));
        } catch (\Exception $e) {
            Log::error('Error loading expense types: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load expense types.');
        }
    }

    /**
     * Currency Settings Page
     */
    public function currency()
    {
        try {
            $settings = $this->settingsService->getSettingsByCategory('currency');
            return view('admin.settings.pages.currency', compact('settings'));
        } catch (\Exception $e) {
            Log::error('Error loading currency settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load currency settings.');
        }
    }

    /**
     * Security Settings Page
     */
    public function security()
    {
        try {
            $settings = $this->settingsService->getSettingsByCategory('security');
            return view('admin.settings.pages.security', compact('settings'));
        } catch (\Exception $e) {
            Log::error('Error loading security settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load security settings.');
        }
    }

    /**
     * Integrations Settings Page
     */
    public function integrations()
    {
        try {
            $integrations = $this->settingsService->getSettingsByCategory('integrations');
            return view('admin.settings.pages.integrations', compact('integrations'));
        } catch (\Exception $e) {
            Log::error('Error loading integrations: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load integrations.');
        }
    }

    /**
     * Backup Settings Page
     */
    public function backup()
    {
        try {
            $backups = \App\Models\Backup::latest()->take(10)->get();
            return view('admin.settings.pages.backup', compact('backups'));
        } catch (\Exception $e) {
            Log::error('Error loading backup settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load backup settings.');
        }
    }

    /**
     * Reports Settings Page
     */
    public function reports()
    {
        try {
            $reportTemplates = \App\Models\ReportTemplate::withCount('reports')->get();
            return view('admin.settings.pages.reports', compact('reportTemplates'));
        } catch (\Exception $e) {
            Log::error('Error loading reports settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load reports settings.');
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
                auth()->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Setting updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update setting'
            ], 500);
        }
    }

    /**
     * Update multiple settings
     */
    public function updateMultiple(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:255',
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
            DB::transaction(function () use ($request) {
                foreach ($request->settings as $setting) {
                    $this->settingsService->setSetting(
                        $setting['key'],
                        $setting['value'],
                        auth()->user()->id
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating multiple settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings'
            ], 500);
        }
    }

    /**
     * Clear settings cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::forget('settings');
            Cache::forget('system_health');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing cache: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    /**
     * Export settings
     */
    public function export(): JsonResponse
    {
        try {
            $settings = $this->settingsService->getAllSettings();
            
            return response()->json([
                'success' => true,
                'settings' => $settings,
                'message' => 'Settings exported successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to export settings'
            ], 500);
        }
    }

    /**
     * Import settings
     */
    public function import(Request $request): JsonResponse
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
            DB::transaction(function () use ($request) {
                foreach ($request->settings as $key => $value) {
                    $this->settingsService->setSetting(
                        $key,
                        $value,
                        auth()->user()->id
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Settings imported successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to import settings'
            ], 500);
        }
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
