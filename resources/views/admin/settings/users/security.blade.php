@extends('layouts.main')

@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-aside-wrap">
                                <div class="card-inner card-inner-lg">
                                    <div class="nk-block-head nk-block-head-lg">
                                        <div class="nk-block-between">
                                            <div class="nk-block-head-content">
                                                <h5 class="nk-block-title">{{ __('Account Security') }}</h5>
                                                <p>{{ __('Configure password policies and account security settings') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="nk-block">
                                        @if($securityGroup && $securityGroup->items->count() > 0)
                                            <div class="card">
                                                <div class="card-body">
                                                    <form id="security-form">
                                                        <div class="row g-3">
                                                            @foreach($securityGroup->items as $item)
                                                                <div class="col-lg-6">
                                                                    <div class="form-group">
                                                                        <label class="form-label" for="{{ $item->key }}">
                                                                            {{ ucwords(str_replace('_', ' ', $item->key)) }}
                                                                            @if($item->is_required)
                                                                                <span class="text-danger">*</span>
                                                                            @endif
                                                                        </label>
                                                                        
                                                                        @if($item->description)
                                                                            <div class="form-note mb-2">{{ $item->description }}</div>
                                                                        @endif

                                                                        <div class="form-control-wrap">
                                                                            @if($item->type === 'boolean')
                                                                                <div class="custom-control custom-switch">
                                                                                    <input type="checkbox" 
                                                                                           class="custom-control-input" 
                                                                                           id="{{ $item->key }}"
                                                                                           data-key="{{ $item->key }}"
                                                                                           {{ $item->value === 'true' ? 'checked' : '' }}>
                                                                                    <label class="custom-control-label" for="{{ $item->key }}">
                                                                                        {{ $item->value === 'true' ? 'Enabled' : 'Disabled' }}
                                                                                    </label>
                                                                                </div>
                                                                            @elseif($item->type === 'select')
                                                                                <select class="form-select" 
                                                                                        id="{{ $item->key }}"
                                                                                        data-key="{{ $item->key }}">
                                                                                    @if($item->options)
                                                                                        @foreach($item->options as $value => $label)
                                                                                            <option value="{{ $value }}" {{ $item->value == $value ? 'selected' : '' }}>
                                                                                                {{ $label }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </select>
                                                                            @elseif($item->type === 'number')
                                                                                <input type="number" 
                                                                                       class="form-control" 
                                                                                       id="{{ $item->key }}"
                                                                                       data-key="{{ $item->key }}"
                                                                                       value="{{ $item->value }}"
                                                                                       placeholder="{{ $item->placeholder ?? '' }}">
                                                                            @else
                                                                                <input type="text" 
                                                                                       class="form-control" 
                                                                                       id="{{ $item->key }}"
                                                                                       data-key="{{ $item->key }}"
                                                                                       value="{{ $item->value }}"
                                                                                       placeholder="{{ $item->placeholder ?? '' }}">
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                {{ __('No security settings found.') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @include('includes.user_settings_menu')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('security-form');
            if (form) {
                form.addEventListener('change', function(e) {
                    if (e.target.hasAttribute('data-key')) {
                        const key = e.target.getAttribute('data-key');
                        let value = e.target.value;
                        
                        if (e.target.type === 'checkbox') {
                            value = e.target.checked ? 'true' : 'false';
                        }
                        
                        updateSetting(key, value);
                    }
                });
            }
            
            function updateSetting(key, value) {
                fetch('{{ route("admin.settings.users.update-setting") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        key: key,
                        value: value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        console.log('Setting updated:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating setting:', error);
                });
            }
        });
    </script>
    @endpush
@endsection
