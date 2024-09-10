<div class="card card-bordered card-full">
    <div class="card-inner">
        <div class="card-title-group align-start mb-0">
            <div class="card-title">
                <h6 class="title">{{ __('Total Properties')}}</h6>
            </div>
            <div class="card-tools">
                <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Total Properties"></em>
            </div>
        </div>
        <div class="card-amount">
            <span class="amount"> {{ $total_properties_count }} </span>
        </div>
        <div class="invest-data mt-1">
            <div class="invest-data-amount g-2">
                <div class="invest-data-history">
                    <div class="title">{{ __('Single Unit')}}</div>
                    <div class="amount">{{ $total_single_units_count }}</div>
                </div>
                <div class="invest-data-history">
                    <div class="title">{{ __('Multi Unit')}}</div>
                    <div class="amount">{{ $total_multi_units_count }}</div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .card -->
