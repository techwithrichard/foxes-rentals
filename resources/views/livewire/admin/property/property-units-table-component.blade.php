<div>
    <div class="nk-block">
        <div class="card ">
            <div class="nk-tb-list nk-tb-ulist is-compact card-bordered">
                <div class="nk-tb-item nk-tb-head">
                    <div class="nk-tb-col tb-col-sm">
                        <span class="sub-text">{{ __('Unit Name')}}</span>
                    </div>
                    <div class="nk-tb-col">
                        <span class="sub-text">{{ __('Type')}}</span>
                    </div>
                    <div class="nk-tb-col tb-col-sm">
                        <span class="sub-text">{{ __('Rent')}} ({{ setting('currency_symbol') }})</span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                        <span class="sub-text">{{ __('Deposit')}} ({{ setting('currency_symbol') }}) </span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                        <span class="sub-text">{{ __('Tenant')}}</span>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                        <span class="sub-text">{{ __('Status')}}</span>
                    </div>

                </div><!-- .nk-tb-item -->

                @forelse($units as $unit)
                    <div class="nk-tb-item">
                        <div class="nk-tb-col tb-col-sm">
                            <span>{{ $unit->name }}</span>
                        </div>
                        <div class="nk-tb-col">
                            <span>{{ $unit->type??'N/a' }}</span>
                        </div>
                        <div class="nk-tb-col tb-col-sm">
                            <span>{{ number_format($unit->rent,2) }}</span>
                        </div>
                        <div class="nk-tb-col tb-col-md">
                            <span>{{ number_format($unit->deposit,2) }}</span>
                        </div>
                        <div class="nk-tb-col tb-col-md">
                            <span>{{ $unit->lease?->tenant?->name ?? ''}}</span>
                        </div>
                        <div class="nk-tb-col">
                            <span>
                                @if ($unit->is_vacant)
                                    <span class="tb-status text-danger">{{ __('Vacant')}}</span>
                                @else
                                    <span class="tb-status text-success">{{ __('Occupied')}}</span>
                                @endif
                            </span>
                        </div>

                    </div><!-- .nk-tb-item -->

                @empty
                @endforelse

            </div><!-- .nk-tb-list -->


            <div class="card-inner">
                <div class="nk-block g-3">
                    <div class="g">
                        {{ $units->links()}}
                    </div>

                </div><!-- .nk-block-between -->
            </div><!-- .card-inner -->

        </div><!-- .card -->
    </div><!-- .nk-block -->
</div>
