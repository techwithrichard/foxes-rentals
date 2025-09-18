<?php

namespace App\Services;

use App\Models\SettingsCategory;
use App\Models\SettingsGroup;
use App\Models\SettingsItem;
use App\Models\SettingsHistory;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class EnhancedSettingsService
{
    protected $cachePrefix = 'enhanced_settings_';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Get all settings organized by categories and groups
     */
    public function getAllSettings()
    {
        return Cache::remember($this->cachePrefix . 'all', $this->cacheTtl, function () {
            return SettingsCategory::with(['activeGroups.activeItems'])
                ->active()
                ->ordered()
                ->get();
        });
    }

    /**
     * Get settings by category
     */
    public function getSettingsByCategory($categorySlug)
    {
        return Cache::remember($this->cachePrefix . 'category_' . $categorySlug, $this->cacheTtl, function () use ($categorySlug) {
            return SettingsCategory::with(['activeGroups.activeItems'])
                ->where('slug', $categorySlug)
                ->active()
                ->first();
        });
    }

    /**
     * Get a specific setting value
     */
    public function getSetting($key, $default = null)
    {
        return Cache::remember($this->cachePrefix . 'item_' . $key, $this->cacheTtl, function () use ($key, $default) {
            $setting = SettingsItem::active()->byKey($key)->first();
            return $setting ? $setting->formatted_value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public function setSetting($key, $value, $userId = null)
    {
        DB::transaction(function () use ($key, $value, $userId) {
            $setting = SettingsItem::byKey($key)->first();
            
            if (!$setting) {
                throw new \Exception("Setting with key '{$key}' not found");
            }

            // Validate value
            if (!$setting->validateValue($value)) {
                throw new \Exception("Invalid value for setting '{$key}'");
            }

            $oldValue = $setting->value;
            $setting->value = $value;
            $setting->save();

            // Log the change
            $this->logSettingChange($setting, $oldValue, $value, $userId);

            // Clear cache
            $this->clearSettingCache($key);
        });
    }

    /**
     * Set multiple settings at once
     */
    public function setMultipleSettings(array $settings, $userId = null)
    {
        DB::transaction(function () use ($settings, $userId) {
            foreach ($settings as $key => $value) {
                $this->setSetting($key, $value, $userId);
            }
        });
    }

    /**
     * Create a new setting
     */
    public function createSetting(array $data)
    {
        return SettingsItem::create($data);
    }

    /**
     * Create a new settings category
     */
    public function createCategory(array $data)
    {
        return SettingsCategory::create($data);
    }

    /**
     * Create a new settings group
     */
    public function createGroup(array $data)
    {
        return SettingsGroup::create($data);
    }

    /**
     * Get settings history
     */
    public function getSettingsHistory($settingKey = null, $userId = null, $days = 30)
    {
        $query = SettingsHistory::with(['setting', 'changedBy'])->recent($days);

        if ($settingKey) {
            $setting = SettingsItem::byKey($settingKey)->first();
            if ($setting) {
                $query->bySetting($setting->id);
            }
        }

        if ($userId) {
            $query->byUser($userId);
        }

        return $query->latest()->get();
    }

    /**
     * Export settings to array
     */
    public function exportSettings()
    {
        $settings = [];
        $categories = $this->getAllSettings();

        foreach ($categories as $category) {
            $settings[$category->slug] = [
                'name' => $category->name,
                'description' => $category->description,
                'groups' => []
            ];

            foreach ($category->activeGroups as $group) {
                $settings[$category->slug]['groups'][$group->slug] = [
                    'name' => $group->name,
                    'description' => $group->description,
                    'settings' => []
                ];

                foreach ($group->activeItems as $item) {
                    $settings[$category->slug]['groups'][$group->slug]['settings'][$item->key] = [
                        'value' => $item->value,
                        'type' => $item->type,
                        'description' => $item->description,
                        'default_value' => $item->default_value
                    ];
                }
            }
        }

        return $settings;
    }

    /**
     * Import settings from array
     */
    public function importSettings(array $settings, $userId = null)
    {
        DB::transaction(function () use ($settings, $userId) {
            foreach ($settings as $categorySlug => $categoryData) {
                if (isset($categoryData['groups'])) {
                    foreach ($categoryData['groups'] as $groupSlug => $groupData) {
                        if (isset($groupData['settings'])) {
                            foreach ($groupData['settings'] as $key => $settingData) {
                                if (isset($settingData['value'])) {
                                    $this->setSetting($key, $settingData['value'], $userId);
                                }
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Clear all settings cache
     */
    public function clearAllCache()
    {
        Cache::forget($this->cachePrefix . 'all');
        
        // Clear category caches
        $categories = SettingsCategory::active()->get();
        foreach ($categories as $category) {
            Cache::forget($this->cachePrefix . 'category_' . $category->slug);
        }

        // Clear individual setting caches
        $settings = SettingsItem::active()->get();
        foreach ($settings as $setting) {
            Cache::forget($this->cachePrefix . 'item_' . $setting->key);
        }
    }

    /**
     * Clear cache for specific setting
     */
    protected function clearSettingCache($key)
    {
        Cache::forget($this->cachePrefix . 'item_' . $key);
        Cache::forget($this->cachePrefix . 'all');
    }

    /**
     * Log setting change
     */
    protected function logSettingChange($setting, $oldValue, $newValue, $userId = null)
    {
        SettingsHistory::create([
            'setting_id' => $setting->id,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_by' => $userId ?: auth()->id(),
            'changed_at' => now(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    /**
     * Get user preferences
     */
    public function getUserPreferences($userId)
    {
        return Cache::remember($this->cachePrefix . 'user_prefs_' . $userId, $this->cacheTtl, function () use ($userId) {
            return DB::table('user_preferences')
                ->where('user_id', $userId)
                ->pluck('preference_value', 'preference_key')
                ->toArray();
        });
    }

    /**
     * Set user preference
     */
    public function setUserPreference($userId, $key, $value)
    {
        DB::table('user_preferences')->updateOrInsert(
            ['user_id' => $userId, 'preference_key' => $key],
            ['preference_value' => $value, 'updated_at' => now()]
        );

        Cache::forget($this->cachePrefix . 'user_prefs_' . $userId);
    }

    /**
     * Get environment-specific settings
     */
    public function getEnvironmentSettings($environment = null)
    {
        $environment = $environment ?: app()->environment();
        
        return Cache::remember($this->cachePrefix . 'env_' . $environment, $this->cacheTtl, function () use ($environment) {
            return DB::table('environment_settings')
                ->where('environment', $environment)
                ->pluck('setting_value', 'setting_key')
                ->toArray();
        });
    }
}
