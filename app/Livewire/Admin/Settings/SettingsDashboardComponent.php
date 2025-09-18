<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Services\EnhancedSettingsService;
use App\Services\ApiKeyManagementService;
use Livewire\Component;
use Livewire\WithPagination;

class SettingsDashboardComponent extends Component
{
    use WithPagination;

    public $systemHealth = [];
    public $recentChanges = [];
    public $statistics = [];
    public $searchQuery = '';
    public $selectedCategory = '';

    protected $listeners = ['refreshDashboard' => 'refreshData'];

    public function mount($systemHealth = [])
    {
        $this->systemHealth = $systemHealth;
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        try {
            $settingsService = app(EnhancedSettingsService::class);
            $apiKeyService = app(ApiKeyManagementService::class);

            // Load statistics
            $this->statistics = [
                'total_settings' => $this->getTotalSettingsCount(),
                'api_keys_count' => $apiKeyService->getApiKeyStatistics()['total_keys'] ?? 0,
                'active_api_keys' => $apiKeyService->getApiKeyStatistics()['active_keys'] ?? 0,
                'expired_api_keys' => $apiKeyService->getApiKeyStatistics()['expired_keys'] ?? 0,
                'users_count' => \App\Models\User::count(),
                'properties_count' => \App\Models\RentalProperty::count() + (\App\Models\SaleProperty::count() ?? 0),
                'system_status' => $this->getOverallSystemStatus()
            ];

            // Load recent changes
            $this->recentChanges = $this->getRecentChanges();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load dashboard data: ' . $e->getMessage());
        }
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->dispatch('dashboardRefreshed');
    }

    public function clearCache()
    {
        try {
            $settingsService = app(EnhancedSettingsService::class);
            $apiKeyService = app(ApiKeyManagementService::class);
            
            $settingsService->clearAllCache();
            $apiKeyService->clearCache();
            
            session()->flash('success', 'Cache cleared successfully!');
            $this->refreshData();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function exportSettings()
    {
        try {
            $settingsService = app(EnhancedSettingsService::class);
            $settings = $settingsService->exportSettings();
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    public function searchSettings()
    {
        // This will be implemented to search through settings
        $this->resetPage();
    }

    public function updatedSearchQuery()
    {
        $this->searchSettings();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    protected function getTotalSettingsCount(): int
    {
        try {
            return \App\Models\SettingsItem::active()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getOverallSystemStatus(): string
    {
        if (empty($this->systemHealth)) {
            return 'unknown';
        }

        $healthyCount = 0;
        $totalCount = 0;

        foreach ($this->systemHealth as $component => $status) {
            if (is_array($status) && isset($status['status'])) {
                $totalCount++;
                if ($status['status'] === 'healthy' || $status['status'] === 'configured') {
                    $healthyCount++;
                }
            }
        }

        if ($totalCount === 0) {
            return 'unknown';
        }

        $healthPercentage = ($healthyCount / $totalCount) * 100;

        if ($healthPercentage >= 90) {
            return 'healthy';
        } elseif ($healthPercentage >= 70) {
            return 'warning';
        } else {
            return 'critical';
        }
    }

    protected function getRecentChanges(): array
    {
        try {
            return \App\Models\SettingsHistory::with(['setting', 'changedBy'])
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($change) {
                    return [
                        'id' => $change->id,
                        'setting_key' => $change->setting->key ?? 'Unknown',
                        'setting_name' => $change->setting->description ?? $change->setting->key ?? 'Unknown',
                        'old_value' => $this->truncateValue($change->old_value),
                        'new_value' => $this->truncateValue($change->new_value),
                        'changed_by' => $change->changedBy->name ?? 'System',
                        'changed_at' => $change->changed_at->diffForHumans(),
                        'ip_address' => $change->ip_address
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function truncateValue($value): string
    {
        if (is_null($value)) {
            return 'null';
        }
        
        $stringValue = is_string($value) ? $value : json_encode($value);
        return strlen($stringValue) > 50 ? substr($stringValue, 0, 50) . '...' : $stringValue;
    }

    public function render()
    {
        return view('livewire.admin.settings.settings-dashboard-component');
    }
}
