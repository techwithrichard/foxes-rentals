<div class="card card-bordered  card-full">
    <div class="card-inner">
        <div class="card-title-group align-start mb-0">
            <div class="card-title">
                <h6 class="title">{{ __('All Houses')}}</h6>
            </div>
            <div class="card-tools">
                <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Total Houses"></em>
            </div>
        </div>
        <div class="card-amount mb-1">
                                                        <span class="amount"> {{ $total_houses_count }}
                                                        </span>
        </div>
        <div class="invest-data">
            <div class="invest-data-amount g-2">
                <div class="invest-data-history">
                    <div class="title">{{ __('Vacant')}}</div>
                    <div class="amount">{{ $total_vacant_houses_count }}</div>
                </div>
                <div class="invest-data-history">
                    <div class="title">{{ __('Occupied')}}</div>
                    <div class="amount">{{ $total_occupied_houses_count  }}</div>
                </div>
                <div class="invest-data-history">
                    <div class="title">{{ __('Maintained')}}</div>
                    <div class="amount">{{ $total_under_maintenance_houses_count }}</div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .card -->
