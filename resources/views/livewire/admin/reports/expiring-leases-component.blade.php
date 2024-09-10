<div class="card card-bordered card-full">
    <div class="card-inner">
        <div class="card-title-group">
            <div class="card-title">
                <h6 class="title">{{ __('Expiring Leases in next 60 days')}}</h6>
            </div>
        </div>
    </div>

    @if($leases->count() > 0)
        <div class="nk-tb-list mt-n2">
            <div class="nk-tb-item nk-tb-head">
                <div class="nk-tb-col"><span>{{ __('Tenant')}}.</span></div>
                <div class="nk-tb-col tb-col-sm"><span>{{ __('Property')}}</span></div>
                <div class="nk-tb-col tb-col-md"><span>{{ __('House')}}</span></div>
                <div class="nk-tb-col"><span>{{ __('Start Date')}}</span></div>
                <div class="nk-tb-col"><span>{{ __('Expires On')}}</span></div>
                <div class="nk-tb-col"><span>{{ __('Rent Cycle')}}</span></div>
                <div class="nk-tb-col"><span class="d-none d-sm-inline"></span></div>
            </div>

            @foreach($leases as $lease)
                <div class="nk-tb-item">
                    <div class="nk-tb-col"><span>{{ $lease->tenant?->name }}</span></div>
                    <div class="nk-tb-col tb-col-sm"><span>{{ $lease->property?->name }}</span></div>
                    <div class="nk-tb-col tb-col-md"><span>{{ $lease->house?->name }}</span></div>
                    <div class="nk-tb-col">
                    <span>
                        {{$lease->start_date?->format('d M, Y')}}
                    </span>
                    </div>
                    <div class="nk-tb-col">
                    <span>
                        {{ $lease->end_date?->format('d M, Y') }}
                    </span>
                    </div>
                    <div class="nk-tb-col">
                    <span>
                        {{ $lease->rent_cycle . ' ' . __($lease->rent_cycle > 1 ? 'months' : 'month') }}
                    </span>
                    </div>
                    <div class="nk-tb-col">
                        <span class="d-none d-sm-inline">
                        <a
                            href="{{ route('admin.leases.show', $lease->id) }}"
                            class="text-soft dropdown-toggle btn btn-sm btn-icon btn-trigger">
                            <em class="icon ni ni-chevron-right"></em>
                        </a>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center m-3">
            {{ $leases->links() }}
        </div>

    @else
        <div class="example-alert px-3 px-2 mb-3">
            <div class="alert alert-gray alert-icon">
                <em class="icon ni ni-alert-circle"></em>
                <strong>{{ __('No Expiring Leases in next 60 days')}}</strong>
            </div>
        </div>

    @endif


</div>
