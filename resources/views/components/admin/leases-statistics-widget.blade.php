<div class="card card-bordered  card-full">
    <div class="card-inner">
        <div class="card-title-group align-start mb-0">
            <div class="card-title">
                <h6 class="title">{{ __('All Leases')}}</h6>
            </div>
            <div class="card-tools">
                <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Total Leases"></em>
            </div>
        </div>
        <div class="card-amount mb-1">
                                                        <span class="amount"> {{ number_format($total_leases_count) }}
                                                        </span>
        </div>
        <div class="invest-data">
            <div class="invest-data-amount g-2">
                <div class="invest-data-history">
                    <div class="title">Active</div>
                    <div class="amount">{{ $total_active_leases_count }}</div>
                </div>
                <div class="invest-data-history">
                    <div class="title">Archived</div>
                    <div class="amount">{{ $total_trashed_leases_count }}</div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .card -->
