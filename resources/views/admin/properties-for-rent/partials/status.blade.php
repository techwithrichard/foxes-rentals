@if($property->is_vacant)
    <span class="badge badge-dot badge-warning">
        <span class="dot"></span>
        <span>{{ __('Vacant') }}</span>
    </span>
@else
    <span class="badge badge-dot badge-success">
        <span class="dot"></span>
        <span>{{ __('Occupied') }}</span>
    </span>
@endif

@if($property->status === 'maintenance')
    <span class="badge badge-dot badge-info ms-2">
        <span class="dot"></span>
        <span>{{ __('Maintenance') }}</span>
    </span>
@endif
