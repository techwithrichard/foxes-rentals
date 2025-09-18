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
                                            <h5 class="nk-block-title">{{ __('Add New API Key') }}</h5>
                                            <span>{{ __('Configure API key for external service integration') }}</span>
                                        </div>
                                        <div class="nk-block-head-content align-self-start d-lg-none">
                                            <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                                               data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                        </div>
                                        <div class="nk-block-head-content">
                                            <a href="{{ route('admin.settings.api-keys.index') }}" class="btn btn-outline-secondary">
                                                <em class="icon ni ni-arrow-left"></em>
                                                <span>{{ __('Back to API Keys') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- API Key Form -->
                                <div class="nk-block">
                                    <form action="{{ route('admin.settings.api-keys.store') }}" method="POST" id="api-key-form">
                                        @csrf
                                        
                                        <div class="row g-3">
                                            <!-- Service Name -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="service_name">
                                                        {{ __('Service Name') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('service_name') is-invalid @enderror" 
                                                            id="service_name" name="service_name" required>
                                                        <option value="">{{ __('Select Service') }}</option>
                                                        @foreach($services as $key => $name)
                                                            <option value="{{ $key }}" {{ old('service_name') === $key ? 'selected' : '' }}>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('service_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-note">{{ __('Choose the external service this API key belongs to') }}</div>
                                                </div>
                                            </div>

                                            <!-- Key Type -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="key_type">
                                                        {{ __('Key Type') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('key_type') is-invalid @enderror" 
                                                            id="key_type" name="key_type" required>
                                                        @foreach($keyTypes as $key => $name)
                                                            <option value="{{ $key }}" {{ old('key_type') === $key ? 'selected' : '' }}>
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('key_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-note">{{ __('Select the type of API key') }}</div>
                                                </div>
                                            </div>

                                            <!-- Environment -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="environment">
                                                        {{ __('Environment') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('environment') is-invalid @enderror" 
                                                            id="environment" name="environment" required>
                                                        @foreach($environments as $env)
                                                            <option value="{{ $env }}" {{ old('environment') === $env ? 'selected' : '' }}>
                                                                {{ ucfirst($env) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('environment')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-note">{{ __('Choose the environment for this API key') }}</div>
                                                </div>
                                            </div>

                                            <!-- API Key Value -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="api_value">
                                                        {{ __('API Key Value') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="form-control-wrap">
                                                        <input type="password" class="form-control @error('api_value') is-invalid @enderror" 
                                                               id="api_value" name="api_value" required 
                                                               value="{{ old('api_value') }}" 
                                                               placeholder="{{ __('Enter your API key') }}">
                                                        <div class="form-control-icon">
                                                            <button type="button" class="btn btn-icon btn-outline-secondary" 
                                                                    onclick="togglePasswordVisibility('api_value')">
                                                                <em class="icon ni ni-eye" id="api_value_icon"></em>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @error('api_value')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-note">{{ __('Enter the actual API key value (will be encrypted)') }}</div>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="form-label" for="description">{{ __('Description') }}</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                                              id="description" name="description" rows="3" 
                                                              placeholder="{{ __('Optional description for this API key') }}">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-note">{{ __('Add a description to help identify this API key') }}</div>
                                                </div>
                                            </div>

                                            <!-- Advanced Settings -->
                                            <div class="col-12">
                                                <div class="card card-bordered">
                                                    <div class="card-header">
                                                        <h6 class="card-title">{{ __('Advanced Settings') }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            <!-- Active Status -->
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" class="custom-control-input" 
                                                                               id="is_active" name="is_active" value="1" 
                                                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="is_active">
                                                                            {{ __('Active') }}
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-note">{{ __('Enable this API key for use') }}</div>
                                                </div>
                                            </div>

                                            <!-- Expiration Date -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="expires_at">{{ __('Expiration Date') }}</label>
                                                    <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                                           id="expires_at" name="expires_at" 
                                                           value="{{ old('expires_at') }}">
                                                    @error('expires_at')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-note">{{ __('Optional expiration date for the API key') }}</div>
                                                </div>
                                            </div>

                                            <!-- Rate Limit -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="rate_limit">{{ __('Rate Limit (requests/minute)') }}</label>
                                                    <input type="number" class="form-control @error('rate_limit') is-invalid @enderror" 
                                                           id="rate_limit" name="rate_limit" 
                                                           value="{{ old('rate_limit') }}" 
                                                           placeholder="{{ __('e.g., 1000') }}">
                                                    @error('rate_limit')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-note">{{ __('Maximum requests per minute (optional)') }}</div>
                                                </div>
                                            </div>

                                            <!-- Allowed IPs -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="allowed_ips">{{ __('Allowed IP Addresses') }}</label>
                                                    <input type="text" class="form-control @error('allowed_ips') is-invalid @enderror" 
                                                           id="allowed_ips" name="allowed_ips" 
                                                           value="{{ old('allowed_ips') }}" 
                                                           placeholder="{{ __('192.168.1.1, 10.0.0.1') }}">
                                                    @error('allowed_ips')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-note">{{ __('Comma-separated list of allowed IP addresses (optional)') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="nk-block">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <div class="btn-group" role="group">
                                                <button type="submit" class="btn btn-primary">
                                                    <em class="icon ni ni-save"></em>
                                                    <span>{{ __('Save API Key') }}</span>
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" onclick="testApiKey()">
                                                    <em class="icon ni ni-play"></em>
                                                    <span>{{ __('Test Connection') }}</span>
                                                </button>
                                                <a href="{{ route('admin.settings.api-keys.index') }}" class="btn btn-outline-light">
                                                    <em class="icon ni ni-cross"></em>
                                                    <span>{{ __('Cancel') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @include('admin.settings.includes.settings-sidebar')
        </div>
    </div>
</div>
</div>
</div>
</div>

@push('scripts')
<script>
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'icon ni ni-eye-off';
    } else {
        input.type = 'password';
        icon.className = 'icon ni ni-eye';
    }
}

function testApiKey() {
    const serviceName = document.getElementById('service_name').value;
    const apiValue = document.getElementById('api_value').value;
    
    if (!serviceName || !apiValue) {
        alert('Please select a service and enter an API key value before testing.');
        return;
    }
    
    // Show loading state
    const testBtn = document.querySelector('button[onclick="testApiKey()"]');
    const originalText = testBtn.innerHTML;
    testBtn.innerHTML = '<em class="icon ni ni-loading"></em> Testing...';
    testBtn.disabled = true;
    
    // Simulate API test (in real implementation, this would make an AJAX call)
    setTimeout(() => {
        testBtn.innerHTML = originalText;
        testBtn.disabled = false;
        
        // For demo purposes, show a success message
        showAlert('API key test completed successfully!', 'success');
    }, 2000);
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Form validation
document.getElementById('api-key-form').addEventListener('submit', function(e) {
    const serviceName = document.getElementById('service_name').value;
    const keyType = document.getElementById('key_type').value;
    const environment = document.getElementById('environment').value;
    const apiValue = document.getElementById('api_value').value;
    
    if (!serviceName || !keyType || !environment || !apiValue) {
        e.preventDefault();
        showAlert('Please fill in all required fields.', 'danger');
        return false;
    }
    
    // Basic API key validation
    if (apiValue.length < 10) {
        e.preventDefault();
        showAlert('API key value must be at least 10 characters long.', 'danger');
        return false;
    }
});

// Auto-fill suggestions based on service selection
document.getElementById('service_name').addEventListener('change', function() {
    const serviceName = this.value;
    const keyTypeSelect = document.getElementById('key_type');
    
    // Reset key type
    keyTypeSelect.selectedIndex = 0;
    
    // Suggest key types based on service
    const suggestions = {
        'mpesa': 'secret',
        'paypal': 'client_secret',
        'stripe': 'secret',
        'sendgrid': 'api_key',
        'twilio': 'token',
        'aws': 'secret',
        'webhook': 'webhook_url'
    };
    
    if (suggestions[serviceName]) {
        const suggestedType = suggestions[serviceName];
        for (let option of keyTypeSelect.options) {
            if (option.value === suggestedType) {
                option.selected = true;
                break;
            }
        }
    }
});
</script>
@endpush
@endsection
