<div class="card card-bordered card-full">
    <div class="card-inner">
        <div class="card-title-group align-start mb-0">
            <div class="card-title">
                <h6 class="title">{{ __('My Properties')}}</h6>
            </div>
            <div class="card-tools">
                <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Your Properties Overview"></em>
            </div>
        </div>
        <div class="card-amount">
            <span class="amount">{{ $totalProperties }}</span>
        </div>
        <div class="invest-data mt-1">
            <div class="invest-data-amount g-2">
                <div class="invest-data-history">
                    <div class="title">{{ __('Occupied')}}</div>
                    <div class="amount">{{ $occupiedProperties }}</div>
                </div>
                <div class="invest-data-history">
                    <div class="title">{{ __('Vacant')}}</div>
                    <div class="amount">{{ $vacantProperties }}</div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .card -->
