@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-aside-wrap">
                            @livewire('admin.settings.settings-dashboard-component', ['systemHealth' => $systemHealth ?? []])
                            @include('admin.settings.includes.settings-sidebar')
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
    // Auto-refresh system health every 5 minutes
    setInterval(function() {
        if (typeof refreshSystemHealth === 'function') {
            refreshSystemHealth();
        }
    }, 300000); // 5 minutes
});
</script>
@endpush
@endsection
