<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use App\Models\PropertyAmenity;
use App\Models\PricingRule;
use App\Models\LeaseTemplate;
use App\Services\PropertySettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PropertySettingsController extends Controller
{
    protected $propertySettingsService;

    public function __construct(PropertySettingsService $propertySettingsService)
    {
        $this->middleware('permission:manage_property_settings');
        $this->propertySettingsService = $propertySettingsService;
    }

    /**
     * Display the property settings dashboard
     */
    public function index()
    {
        try {
            $propertyTypes = PropertyType::withCount(['rentalProperties', 'saleProperties'])->get();
            $amenities = PropertyAmenity::withCount('properties')->get();
            $pricingRules = PricingRule::active()->get();
            $leaseTemplates = LeaseTemplate::active()->get();
            $statistics = $this->propertySettingsService->getPropertyStatistics();

            return view('admin.settings.property.dashboard', compact(
                'propertyTypes', 'amenities', 'pricingRules', 'leaseTemplates', 'statistics'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading property settings: ' . $e->getMessage());
            
            // Return with empty data to prevent undefined variable errors
            $propertyTypes = collect();
            $amenities = collect();
            $pricingRules = collect();
            $leaseTemplates = collect();
            $statistics = [
                'property_types' => ['total' => 0, 'active' => 0],
                'amenities' => ['total' => 0, 'active' => 0, 'categories' => 0],
                'pricing_rules' => ['total' => 0, 'active' => 0],
                'lease_templates' => ['total' => 0, 'active' => 0]
            ];
            
            return view('admin.settings.property.dashboard', compact(
                'propertyTypes', 'amenities', 'pricingRules', 'leaseTemplates', 'statistics'
            ))->with('error', 'Failed to load property settings: ' . $e->getMessage());
        }
    }

    /**
     * Property Types Management
     */
    public function propertyTypes()
    {
        $propertyTypes = PropertyType::withCount(['rentalProperties', 'saleProperties'])
            ->orderBy('name')
            ->get();

        return view('admin.settings.property.property-types.index', compact('propertyTypes'));
    }

    public function createPropertyType()
    {
        return view('admin.settings.property.property-types.create');
    }

    public function storePropertyType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:property_types,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            PropertyType::create([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
                'features' => $request->features ?? [],
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $request->sort_order ?? 0
            ]);

            Log::info("Property type created: {$request->name} by user " . auth()->id());

            return redirect()->route('admin.settings.property.types')
                ->with('success', 'Property type created successfully.');

        } catch (\Exception $e) {
            Log::error("Error creating property type: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create property type. Please try again.'])
                ->withInput();
        }
    }

    public function editPropertyType(PropertyType $propertyType)
    {
        return view('admin.settings.property.property-types.edit', compact('propertyType'));
    }

    public function updatePropertyType(Request $request, PropertyType $propertyType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:property_types,name,' . $propertyType->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $propertyType->update([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
                'features' => $request->features ?? [],
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $request->sort_order ?? 0
            ]);

            Log::info("Property type updated: {$propertyType->name} by user " . auth()->id());

            return redirect()->route('admin.settings.property.types')
                ->with('success', 'Property type updated successfully.');

        } catch (\Exception $e) {
            Log::error("Error updating property type: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update property type. Please try again.'])
                ->withInput();
        }
    }

    public function destroyPropertyType(PropertyType $propertyType)
    {
        try {
            $propertyTypeName = $propertyType->name;
            $propertyType->delete();

            Log::info("Property type deleted: {$propertyTypeName} by user " . auth()->id());

            return redirect()->route('admin.settings.property.types')
                ->with('success', 'Property type deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Error deleting property type: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete property type. Please try again.']);
        }
    }

    /**
     * Amenities Management
     */
    public function amenities()
    {
        $amenities = PropertyAmenity::withCount('properties')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('admin.settings.property.amenities.index', compact('amenities'));
    }

    public function createAmenity()
    {
        $categories = PropertyAmenity::distinct()->pluck('category')->filter()->sort()->values();
        return view('admin.settings.property.amenities.create', compact('categories'));
    }

    public function storeAmenity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:property_amenities,name',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'icon' => 'nullable|string|max:100',
            'is_chargeable' => 'boolean',
            'default_cost' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            PropertyAmenity::create([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'icon' => $request->icon,
                'is_chargeable' => $request->boolean('is_chargeable', false),
                'default_cost' => $request->default_cost,
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $request->sort_order ?? 0
            ]);

            Log::info("Property amenity created: {$request->name} by user " . auth()->id());

            return redirect()->route('admin.settings.property.amenities')
                ->with('success', 'Property amenity created successfully.');

        } catch (\Exception $e) {
            Log::error("Error creating property amenity: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create property amenity. Please try again.'])
                ->withInput();
        }
    }

    public function editAmenity(PropertyAmenity $amenity)
    {
        $categories = PropertyAmenity::distinct()->pluck('category')->filter()->sort()->values();
        return view('admin.settings.property.amenities.edit', compact('amenity', 'categories'));
    }

    public function updateAmenity(Request $request, PropertyAmenity $amenity)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:property_amenities,name,' . $amenity->id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'icon' => 'nullable|string|max:100',
            'is_chargeable' => 'boolean',
            'default_cost' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $amenity->update([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'icon' => $request->icon,
                'is_chargeable' => $request->boolean('is_chargeable', false),
                'default_cost' => $request->default_cost,
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $request->sort_order ?? 0
            ]);

            Log::info("Property amenity updated: {$amenity->name} by user " . auth()->id());

            return redirect()->route('admin.settings.property.amenities')
                ->with('success', 'Property amenity updated successfully.');

        } catch (\Exception $e) {
            Log::error("Error updating property amenity: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update property amenity. Please try again.'])
                ->withInput();
        }
    }

    public function destroyAmenity(PropertyAmenity $amenity)
    {
        try {
            $amenityName = $amenity->name;
            $amenity->delete();

            Log::info("Property amenity deleted: {$amenityName} by user " . auth()->id());

            return redirect()->route('admin.settings.property.amenities')
                ->with('success', 'Property amenity deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Error deleting property amenity: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete property amenity. Please try again.']);
        }
    }

    /**
     * Pricing Rules Management
     */
    public function pricingRules()
    {
        $pricingRules = PricingRule::with(['conditions'])
            ->orderBy('name')
            ->get();

        return view('admin.settings.property.pricing.index', compact('pricingRules'));
    }

    public function createPricingRule()
    {
        $propertyTypes = PropertyType::active()->get();
        $amenities = PropertyAmenity::active()->get();
        
        return view('admin.settings.property.pricing.create', compact('propertyTypes', 'amenities'));
    }

    public function storePricingRule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:pricing_rules,name',
            'description' => 'nullable|string|max:1000',
            'rule_type' => 'required|in:commission,late_fee,deposit,renewal_fee,maintenance_fee',
            'conditions' => 'nullable|array',
            'calculation_method' => 'required|in:percentage,fixed_amount,sliding_scale',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            PricingRule::create([
                'name' => $request->name,
                'description' => $request->description,
                'rule_type' => $request->rule_type,
                'conditions' => $request->conditions ?? [],
                'calculation_method' => $request->calculation_method,
                'value' => $request->value,
                'is_active' => $request->boolean('is_active', true)
            ]);

            Log::info("Pricing rule created: {$request->name} by user " . auth()->id());

            return redirect()->route('admin.settings.property.pricing')
                ->with('success', 'Pricing rule created successfully.');

        } catch (\Exception $e) {
            Log::error("Error creating pricing rule: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create pricing rule. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Lease Templates Management
     */
    public function leaseTemplates()
    {
        $leaseTemplates = LeaseTemplate::withCount('leases')
            ->orderBy('name')
            ->get();

        return view('admin.settings.property.lease-templates.index', compact('leaseTemplates'));
    }

    public function createLeaseTemplate()
    {
        return view('admin.settings.property.lease-templates.create');
    }

    public function storeLeaseTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:lease_templates,name',
            'description' => 'nullable|string|max:1000',
            'template_type' => 'required|in:residential,commercial,short_term,long_term',
            'content' => 'required|string',
            'terms' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            LeaseTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
                'template_type' => $request->template_type,
                'content' => $request->content,
                'terms' => $request->terms ?? [],
                'is_active' => $request->boolean('is_active', true)
            ]);

            Log::info("Lease template created: {$request->name} by user " . auth()->id());

            return redirect()->route('admin.settings.property.lease-templates')
                ->with('success', 'Lease template created successfully.');

        } catch (\Exception $e) {
            Log::error("Error creating lease template: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create lease template. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Bulk operations for property settings
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'type' => 'required|in:property_types,amenities,pricing_rules,lease_templates',
            'ids' => 'required|array|min:1',
            'ids.*' => 'uuid|exists:' . $request->type . ',id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $modelClass = $this->getModelClass($request->type);
            $items = $modelClass::whereIn('id', $request->ids)->get();

            switch ($request->action) {
                case 'activate':
                    foreach ($items as $item) {
                        $item->update(['is_active' => true]);
                    }
                    $message = 'Selected items activated successfully.';
                    break;

                case 'deactivate':
                    foreach ($items as $item) {
                        $item->update(['is_active' => false]);
                    }
                    $message = 'Selected items deactivated successfully.';
                    break;

                case 'delete':
                    foreach ($items as $item) {
                        $item->delete();
                    }
                    $message = 'Selected items deleted successfully.';
                    break;
            }

            Log::info("Bulk action '{$request->action}' performed on {$request->type} by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error("Error in bulk action: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action'
            ], 400);
        }
    }

    /**
     * Get property settings statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->propertySettingsService->getPropertyStatistics();
            
            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting property statistics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics'
            ], 400);
        }
    }

    /**
     * Export property settings
     */
    public function exportSettings(): JsonResponse
    {
        try {
            $settings = $this->propertySettingsService->exportPropertySettings();
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error("Error exporting property settings: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to export settings'
            ], 400);
        }
    }

    /**
     * Import property settings
     */
    public function importSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'overwrite' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->propertySettingsService->importPropertySettings(
                $request->settings,
                $request->boolean('overwrite', false),
                auth()->id()
            );

            Log::info("Property settings imported by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Property settings imported successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Error importing property settings: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get model class based on type
     */
    protected function getModelClass(string $type): string
    {
        $models = [
            'property_types' => PropertyType::class,
            'amenities' => PropertyAmenity::class,
            'pricing_rules' => PricingRule::class,
            'lease_templates' => LeaseTemplate::class
        ];

        return $models[$type] ?? throw new \InvalidArgumentException("Invalid type: {$type}");
    }
}
