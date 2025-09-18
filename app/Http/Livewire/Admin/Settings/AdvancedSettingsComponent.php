<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Models\SettingsCategory;
use App\Models\SettingsItem;
use App\Services\EnhancedSettingsService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class AdvancedSettingsComponent extends Component
{
    use LivewireAlert;

    public $categories = [];
    public $selectedCategory = null;
    public $selectedGroup = null;
    public $settings = [];
    public $searchTerm = '';
    public $showHistory = false;
    public $historyData = [];
    public $loading = false;

    protected $listeners = ['refreshSettings', 'settingUpdated'];

    public function mount($categorySlug = null)
    {
        $this->loadSettings();
        
        if ($categorySlug) {
            $this->selectCategory($categorySlug);
        }
    }

    public function loadSettings()
    {
        $this->loading = true;
        
        try {
            $this->categories = app(EnhancedSettingsService::class)->getAllSettings();
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to load settings: ' . $e->getMessage());
        }
        
        $this->loading = false;
    }

    public function selectCategory($categorySlug)
    {
        $this->selectedCategory = $this->categories->firstWhere('slug', $categorySlug);
        $this->selectedGroup = null;
        $this->settings = [];
        
        if ($this->selectedCategory) {
            $this->settings = $this->selectedCategory->activeGroups->flatMap(function ($group) {
                return $group->activeItems->map(function ($item) use ($group) {
                    return [
                        'id' => $item->id,
                        'key' => $item->key,
                        'value' => $item->value,
                        'type' => $item->type,
                        'description' => $item->description,
                        'group_name' => $group->name,
                        'group_slug' => $group->slug,
                        'options' => $item->options,
                        'placeholder' => $item->placeholder,
                        'is_required' => $item->is_required,
                        'validation_rules' => $item->validation_rules
                    ];
                });
            })->keyBy('key');
        }
    }

    public function selectGroup($groupSlug)
    {
        if (!$this->selectedCategory) return;
        
        $this->selectedGroup = $this->selectedCategory->activeGroups->firstWhere('slug', $groupSlug);
        
        if ($this->selectedGroup) {
            $this->settings = $this->selectedGroup->activeItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'key' => $item->key,
                    'value' => $item->value,
                    'type' => $item->type,
                    'description' => $item->description,
                    'group_name' => $this->selectedGroup->name,
                    'group_slug' => $this->selectedGroup->slug,
                    'options' => $item->options,
                    'placeholder' => $item->placeholder,
                    'is_required' => $item->is_required,
                    'validation_rules' => $item->validation_rules
                ];
            })->keyBy('key');
        }
    }

    public function updateSetting($key, $value)
    {
        $this->loading = true;
        
        try {
            app(EnhancedSettingsService::class)->setSetting($key, $value, auth()->id());
            
            // Update local settings
            if (isset($this->settings[$key])) {
                $this->settings[$key]['value'] = $value;
            }
            
            $this->alert('success', 'Setting updated successfully');
            $this->emit('settingUpdated', $key, $value);
            
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to update setting: ' . $e->getMessage());
        }
        
        $this->loading = false;
    }

    public function updateMultipleSettings()
    {
        $this->loading = true;
        
        try {
            $settingsToUpdate = [];
            foreach ($this->settings as $key => $setting) {
                $settingsToUpdate[$key] = $setting['value'];
            }
            
            app(EnhancedSettingsService::class)->setMultipleSettings($settingsToUpdate, auth()->id());
            
            $this->alert('success', 'Settings updated successfully');
            
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to update settings: ' . $e->getMessage());
        }
        
        $this->loading = false;
    }

    public function resetToDefault($key)
    {
        $setting = SettingsItem::byKey($key)->first();
        
        if ($setting && $setting->default_value !== null) {
            $this->updateSetting($key, $setting->default_value);
        }
    }

    public function showSettingHistory($key)
    {
        try {
            $this->historyData = app(EnhancedSettingsService::class)->getSettingsHistory($key);
            $this->showHistory = true;
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to load history: ' . $e->getMessage());
        }
    }

    public function clearCache()
    {
        try {
            app(EnhancedSettingsService::class)->clearAllCache();
            $this->alert('success', 'Settings cache cleared successfully');
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function exportSettings()
    {
        try {
            $settings = app(EnhancedSettingsService::class)->exportSettings();
            
            return response()->streamDownload(function () use ($settings) {
                echo json_encode($settings, JSON_PRETTY_PRINT);
            }, 'settings_export_' . now()->format('Y-m-d_H-i-s') . '.json');
            
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    public function refreshSettings()
    {
        $this->loadSettings();
        if ($this->selectedCategory) {
            $this->selectCategory($this->selectedCategory->slug);
        }
    }

    public function settingUpdated($key, $value)
    {
        if (isset($this->settings[$key])) {
            $this->settings[$key]['value'] = $value;
        }
    }

    public function getFilteredSettingsProperty()
    {
        if (empty($this->searchTerm)) {
            return $this->settings;
        }

        return collect($this->settings)->filter(function ($setting) {
            return str_contains(strtolower($setting['key']), strtolower($this->searchTerm)) ||
                   str_contains(strtolower($setting['description']), strtolower($this->searchTerm)) ||
                   str_contains(strtolower($setting['group_name']), strtolower($this->searchTerm));
        });
    }

    public function render()
    {
        return view('livewire.admin.settings.advanced-settings-component', [
            'filteredSettings' => $this->filteredSettings
        ]);
    }
}