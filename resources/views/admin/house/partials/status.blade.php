@switch($house->status)
    @case(\App\Enums\HouseStatusEnum::VACANT->value)
    <span class="badge rounded-pill bg-danger">{{ __('Vacant')}}</span>
    @break
    @case(\App\Enums\HouseStatusEnum::OCCUPIED->value)
    <span class="badge rounded-pill bg-success">{{ __('Rented')}}</span>
    @break
    @case(\App\Enums\HouseStatusEnum::UNDER_MAINTENANCE->value)
    <span class="badge rounded-pill bg-info">{{ __('Under Maintenance')}}</span>
    @break
    @default
    <span class="badge rounded-pill bg-info">{{ __('Unknown')}}</span>
@endswitch
