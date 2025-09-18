@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <!-- Breadcrumb -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <nav>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('General Settings') }}</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-primary btn-sm">
                                <em class="icon ni ni-arrow-left"></em>
                                <span>{{ __('Back to Settings') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Settings Form -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-header">
                            <h6 class="card-title">{{ __('General Application Settings') }}</h6>
                            <div class="card-tools">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveSettings()">
                                    <em class="icon ni ni-save"></em>
                                    <span>{{ __('Save Changes') }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="generalSettingsForm">
                                <div class="row g-3">
                                    <!-- Application Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Application Name') }}</label>
                                            <input type="text" class="form-control" name="app_name" 
                                                   value="{{ setting('app_name', 'Foxes Rental Management') }}" 
                                                   placeholder="{{ __('Enter application name') }}">
                                            <div class="form-note">{{ __('This name will appear in the browser title and emails') }}</div>
                                        </div>
                                    </div>

                                    <!-- Application URL -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Application URL') }}</label>
                                            <input type="url" class="form-control" name="app_url" 
                                                   value="{{ setting('app_url', url('/')) }}" 
                                                   placeholder="{{ __('https://your-domain.com') }}">
                                            <div class="form-note">{{ __('Base URL for your application') }}</div>
                                        </div>
                                    </div>

                                    <!-- Timezone -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Timezone') }}</label>
                                            <select class="form-select" name="timezone">
                                                <option value="Africa/Nairobi" {{ setting('timezone') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi</option>
                                                <option value="UTC" {{ setting('timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                                <option value="America/New_York" {{ setting('timezone') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                                <option value="Europe/London" {{ setting('timezone') === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                            </select>
                                            <div class="form-note">{{ __('Default timezone for the application') }}</div>
                                        </div>
                                    </div>

                                    <!-- Language -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Default Language') }}</label>
                                            <select class="form-select" name="locale">
                                                <option value="en" {{ setting('locale') === 'en' ? 'selected' : '' }}>English</option>
                                                <option value="sw" {{ setting('locale') === 'sw' ? 'selected' : '' }}>Kiswahili</option>
                                            </select>
                                            <div class="form-note">{{ __('Default language for the application') }}</div>
                                        </div>
                                    </div>

                                    <!-- Maintenance Mode -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Maintenance Mode') }}</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="maintenance_mode" 
                                                       {{ setting('maintenance_mode') ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ __('Enable maintenance mode') }}</label>
                                            </div>
                                            <div class="form-note">{{ __('When enabled, only administrators can access the application') }}</div>
                                        </div>
                                    </div>

                                    <!-- Debug Mode -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Debug Mode') }}</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="debug_mode" 
                                                       {{ setting('debug_mode') ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ __('Enable debug mode') }}</label>
                                            </div>
                                            <div class="form-note">{{ __('Show detailed error messages (disable in production)') }}</div>
                                        </div>
                                    </div>

                                    <!-- Email Settings -->
                                    <div class="col-12">
                                        <hr>
                                        <h6 class="title">{{ __('Email Configuration') }}</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('From Email Address') }}</label>
                                            <input type="email" class="form-control" name="mail_from_address" 
                                                   value="{{ setting('mail_from_address', 'noreply@foxesrental.com') }}" 
                                                   placeholder="{{ __('noreply@yourdomain.com') }}">
                                            <div class="form-note">{{ __('Default sender email address') }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('From Name') }}</label>
                                            <input type="text" class="form-control" name="mail_from_name" 
                                                   value="{{ setting('mail_from_name', 'Foxes Rental Management') }}" 
                                                   placeholder="{{ __('Your Company Name') }}">
                                            <div class="form-note">{{ __('Default sender name') }}</div>
                                        </div>
                                    </div>

                                    <!-- Notification Settings -->
                                    <div class="col-12">
                                        <hr>
                                        <h6 class="title">{{ __('Notification Settings') }}</h6>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Email Notifications') }}</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="email_notifications" 
                                                       {{ setting('email_notifications', true) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ __('Enable email notifications') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('SMS Notifications') }}</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="sms_notifications" 
                                                       {{ setting('sms_notifications') ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ __('Enable SMS notifications') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Push Notifications') }}</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="push_notifications" 
                                                       {{ setting('push_notifications') ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ __('Enable push notifications') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function saveSettings() {
    const form = document.getElementById('generalSettingsForm');
    const formData = new FormData(form);
    const settings = {};
    
    // Convert form data to settings object
    for (let [key, value] of formData.entries()) {
        settings[key] = value;
    }
    
    // Show loading state
    const saveBtn = document.querySelector('button[onclick="saveSettings()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<em class="icon ni ni-loading"></em> Saving...';
    saveBtn.disabled = true;
    
    fetch('{{ route("admin.settings.update-multiple") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ settings: settings })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Settings saved successfully!', 'success');
        } else {
            showNotification('Failed to save settings: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while saving settings', 'danger');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
</script>
@endpush
@endsection
