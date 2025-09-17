@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ __('Welcome to Your Dashboard') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Redirecting you to your appropriate portal...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-lg bg-primary mb-3">
                                <em class="icon ni ni-user"></em>
                            </div>
                            <h4>{{ __('Redirecting...') }}</h4>
                            <p class="text-muted">{{ __('Please wait while we redirect you to your dashboard.') }}</p>
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ __('Loading...') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-redirect after 2 seconds
setTimeout(function() {
    window.location.href = '{{ route("dashboard") }}';
}, 2000);
</script>
@endsection