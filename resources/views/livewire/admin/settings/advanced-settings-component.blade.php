<div class="card-inner card-inner-lg">
    <div class="nk-block-head nk-block-head-lg">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">{{ __('Advanced Settings') }}</h5>
                <span>{{ __('Comprehensive system configuration and management') }}</span>
            </div>
        </div>
    </div>

    <!-- Search and Actions Bar -->
    <div class="nk-block">
        <div class="row g-3 align-center mb-4">
            <div class="col-lg-6">
                <div class="form-group">
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-search"></em>
                        </div>
                        <input type="text" class="form-control" placeholder="Search settings..." 
                               wire:model.debounce.300ms="searchTerm">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex gap-2 justify-content-end">
                    <button class="btn btn-outline-primary" wire:click="clearCache" wire:loading.attr="disabled">
                        <em class="icon ni ni-refresh"></em>
                        <span>Clear Cache</span>
                    </button>
                    <button class="btn btn-primary" wire:click="updateMultipleSettings" wire:loading.attr="disabled">
                        <em class="icon ni ni-save"></em>
                        <span>Save All</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Categories Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="nav-tabs-s2">
                    <ul class="nav nav-tabs nav-tabs-s2" role="tablist">
                        @foreach($categories as $category)
                            <li class="nav-item">
                                <a class="nav-link {{ $selectedCategory && $selectedCategory->slug === $category->slug ? 'active' : '' }}"
                                   wire:click="selectCategory('{{ $category->slug }}')" href="#">
                                    <em class="icon {{ $category->icon }}"></em>
                                    <span>{{ $category->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        @if($selectedCategory)
            <!-- Settings Form -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if($loading)
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            @else
                                <form wire:submit.prevent="updateMultipleSettings">
                                    <div class="row g-3">
                                        @forelse($filteredSettings as $setting)
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="setting_{{ $setting['key'] }}">
                                                        {{ ucwords(str_replace('_', ' ', $setting['key'])) }}
                                                        @if($setting['is_required'])
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    
                                                    @if($setting['description'])
                                                        <div class="form-note mb-2">{{ $setting['description'] }}</div>
                                                    @endif

                                                    <div class="form-control-wrap">
                                                        @if($setting['type'] === 'boolean')
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" 
                                                                       class="custom-control-input" 
                                                                       id="setting_{{ $setting['key'] }}"
                                                                       wire:change="updateSetting('{{ $setting['key'] }}', $event.target.checked ? 'true' : 'false')"
                                                                       {{ $setting['value'] === 'true' ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="setting_{{ $setting['key'] }}">
                                                                    {{ $setting['value'] === 'true' ? 'Enabled' : 'Disabled' }}
                                                                </label>
                                                            </div>
                                                        @elseif($setting['type'] === 'select')
                                                            <select class="form-select" 
                                                                    id="setting_{{ $setting['key'] }}"
                                                                    wire:change="updateSetting('{{ $setting['key'] }}', $event.target.value)">
                                                                @if($setting['options'])
                                                                    @foreach($setting['options'] as $value => $label)
                                                                        <option value="{{ $value }}" {{ $setting['value'] == $value ? 'selected' : '' }}>
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        @elseif($setting['type'] === 'number')
                                                            <input type="number" 
                                                                   class="form-control" 
                                                                   id="setting_{{ $setting['key'] }}"
                                                                   wire:model.defer="settings.{{ $setting['key'] }}.value"
                                                                   placeholder="{{ $setting['placeholder'] ?? '' }}">
                                                        @else
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   id="setting_{{ $setting['key'] }}"
                                                                   wire:model.defer="settings.{{ $setting['key'] }}.value"
                                                                   placeholder="{{ $setting['placeholder'] ?? '' }}">
                                                        @endif
                                                    </div>

                                                    <div class="form-note mt-1">
                                                        <small class="text-muted">
                                                            Group: {{ $setting['group_name'] }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="text-center py-4">
                                                    <em class="icon ni ni-inbox icon-lg text-muted"></em>
                                                    <p class="text-muted mt-2">No settings found matching your search criteria.</p>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Welcome Message -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <em class="icon ni ni-settings icon-lg text-primary mb-3"></em>
                            <h4>Welcome to Advanced Settings</h4>
                            <p class="text-muted">Select a category from the tabs above to configure your system settings.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>