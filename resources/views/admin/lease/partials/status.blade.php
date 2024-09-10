@if ($property->is_multi_unit)
    <span class="badge rounded-pill bg-primary">{{__('Multi-Unit 36')}}</span>
@else

    @if ($property->is_vacant)
        <span class="badge rounded-pill bg-danger">{{ __('Vacant')}}</span>
    @else
        <span class="badge rounded-pill bg-success">{{ __('Occupied')}}</span>
    @endif
@endif
