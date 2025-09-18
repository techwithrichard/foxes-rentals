<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Services\ApiKeyManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiKeysController extends Controller
{
    protected $apiKeyService;

    public function __construct(ApiKeyManagementService $apiKeyService)
    {
        $this->middleware('permission:manage_api_keys');
        $this->apiKeyService = $apiKeyService;
    }

    /**
     * Display a listing of API keys
     */
    public function index(Request $request)
    {
        $query = ApiKey::with(['creator', 'lastUsedBy']);

        // Filter by service
        if ($request->filled('service')) {
            $query->where('service_name', 'like', '%' . $request->service . '%');
        }

        // Filter by environment
        if ($request->filled('environment')) {
            $query->where('environment', $request->environment);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            }
        }

        $apiKeys = $query->orderBy('created_at', 'desc')->paginate(20);
        $services = ApiKey::distinct()->pluck('service_name')->sort()->values();
        $environments = ['production', 'staging', 'development'];

        return view('admin.settings.api-keys.index', compact('apiKeys', 'services', 'environments'));
    }

    /**
     * Show the form for creating a new API key
     */
    public function create()
    {
        $services = $this->apiKeyService->getAvailableServices();
        $keyTypes = $this->apiKeyService->getKeyTypes();
        $environments = ['production', 'staging', 'development'];

        return view('admin.settings.api-keys.create', compact('services', 'keyTypes', 'environments'));
    }

    /**
     * Store a newly created API key
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_name' => 'required|string|max:255',
            'key_type' => 'required|in:api_key,secret,token,webhook_url,client_id,client_secret',
            'environment' => 'required|in:production,staging,development',
            'api_value' => 'required|string|min:10',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
            'rate_limit' => 'nullable|integer|min:1',
            'allowed_ips' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Validate API key format
            if (!$this->apiKeyService->validateApiKey($request->service_name, $request->api_value)) {
                return redirect()->back()
                    ->withErrors(['api_value' => 'Invalid API key format for ' . $request->service_name])
                    ->withInput();
            }

            // Parse allowed IPs
            $allowedIps = null;
            if ($request->filled('allowed_ips')) {
                $allowedIps = array_filter(array_map('trim', explode(',', $request->allowed_ips)));
                if (!empty($allowedIps)) {
                    // Validate IP addresses
                    foreach ($allowedIps as $ip) {
                        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                            return redirect()->back()
                                ->withErrors(['allowed_ips' => "Invalid IP address: {$ip}"])
                                ->withInput();
                        }
                    }
                }
            }

            ApiKey::create([
                'service_name' => $request->service_name,
                'key_type' => $request->key_type,
                'environment' => $request->environment,
                'encrypted_value' => encrypt($request->api_value),
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
                'expires_at' => $request->expires_at,
                'rate_limit' => $request->rate_limit,
                'allowed_ips' => $allowedIps,
                'created_by' => auth()->id()
            ]);

            Log::info("API key created for service: {$request->service_name} by user " . auth()->id());

            return redirect()->route('admin.settings.api-keys.index')
                ->with('success', 'API key created successfully.');

        } catch (\Exception $e) {
            Log::error("Error creating API key: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create API key. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified API key
     */
    public function show(ApiKey $apiKey)
    {
        $apiKey->load(['creator', 'lastUsedBy']);
        return view('admin.settings.api-keys.show', compact('apiKey'));
    }

    /**
     * Show the form for editing the specified API key
     */
    public function edit(ApiKey $apiKey)
    {
        $services = $this->apiKeyService->getAvailableServices();
        $keyTypes = $this->apiKeyService->getKeyTypes();
        $environments = ['production', 'staging', 'development'];

        return view('admin.settings.api-keys.edit', compact('apiKey', 'services', 'keyTypes', 'environments'));
    }

    /**
     * Update the specified API key
     */
    public function update(Request $request, ApiKey $apiKey)
    {
        $validator = Validator::make($request->all(), [
            'service_name' => 'required|string|max:255',
            'key_type' => 'required|in:api_key,secret,token,webhook_url,client_id,client_secret',
            'environment' => 'required|in:production,staging,development',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
            'rate_limit' => 'nullable|integer|min:1',
            'allowed_ips' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Parse allowed IPs
            $allowedIps = null;
            if ($request->filled('allowed_ips')) {
                $allowedIps = array_filter(array_map('trim', explode(',', $request->allowed_ips)));
                if (!empty($allowedIps)) {
                    // Validate IP addresses
                    foreach ($allowedIps as $ip) {
                        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                            return redirect()->back()
                                ->withErrors(['allowed_ips' => "Invalid IP address: {$ip}"])
                                ->withInput();
                        }
                    }
                }
            }

            $apiKey->update([
                'service_name' => $request->service_name,
                'key_type' => $request->key_type,
                'environment' => $request->environment,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
                'expires_at' => $request->expires_at,
                'rate_limit' => $request->rate_limit,
                'allowed_ips' => $allowedIps
            ]);

            Log::info("API key updated: {$apiKey->id} by user " . auth()->id());

            return redirect()->route('admin.settings.api-keys.index')
                ->with('success', 'API key updated successfully.');

        } catch (\Exception $e) {
            Log::error("Error updating API key: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update API key. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified API key from storage
     */
    public function destroy(ApiKey $apiKey)
    {
        try {
            $serviceName = $apiKey->service_name;
            $apiKey->delete();

            Log::info("API key deleted for service: {$serviceName} by user " . auth()->id());

            return redirect()->route('admin.settings.api-keys.index')
                ->with('success', 'API key deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Error deleting API key: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete API key. Please try again.']);
        }
    }

    /**
     * Toggle API key active status
     */
    public function toggleStatus(ApiKey $apiKey): JsonResponse
    {
        try {
            $apiKey->update(['is_active' => !$apiKey->is_active]);
            
            $status = $apiKey->is_active ? 'activated' : 'deactivated';
            Log::info("API key {$status}: {$apiKey->id} by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => "API key {$status} successfully",
                'is_active' => $apiKey->is_active
            ]);

        } catch (\Exception $e) {
            Log::error("Error toggling API key status: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update API key status'
            ], 400);
        }
    }

    /**
     * Test API key connectivity
     */
    public function testConnection(ApiKey $apiKey): JsonResponse
    {
        try {
            $result = $this->apiKeyService->testApiKey($apiKey);
            
            return response()->json([
                'success' => true,
                'message' => 'Connection test completed',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error("Error testing API key connection: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Regenerate API key value
     */
    public function regenerate(ApiKey $apiKey, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'new_value' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validate new API key format
            if (!$this->apiKeyService->validateApiKey($apiKey->service_name, $request->new_value)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key format for ' . $apiKey->service_name
                ], 400);
            }

            $apiKey->update([
                'encrypted_value' => encrypt($request->new_value),
                'last_used_at' => null,
                'usage_count' => 0
            ]);

            Log::info("API key regenerated: {$apiKey->id} by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'API key regenerated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Error regenerating API key: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate API key'
            ], 400);
        }
    }

    /**
     * Bulk actions on API keys
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'api_key_ids' => 'required|array|min:1',
            'api_key_ids.*' => 'exists:api_keys,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $apiKeys = ApiKey::whereIn('id', $request->api_key_ids)->get();

            switch ($request->action) {
                case 'activate':
                    foreach ($apiKeys as $apiKey) {
                        $apiKey->update(['is_active' => true]);
                    }
                    $message = 'Selected API keys activated successfully.';
                    break;

                case 'deactivate':
                    foreach ($apiKeys as $apiKey) {
                        $apiKey->update(['is_active' => false]);
                    }
                    $message = 'Selected API keys deactivated successfully.';
                    break;

                case 'delete':
                    foreach ($apiKeys as $apiKey) {
                        $apiKey->delete();
                    }
                    $message = 'Selected API keys deleted successfully.';
                    break;
            }

            Log::info("Bulk API key action '{$request->action}' performed by user " . auth()->id() . " on " . count($apiKeys) . " keys");

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error("Error in bulk API key action: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action'
            ], 400);
        }
    }
}
