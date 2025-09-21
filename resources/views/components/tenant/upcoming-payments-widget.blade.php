<div class="card card-bordered card-full">
    <div class="card-inner">
        <div class="card-title-group align-start mb-0">
            <div class="card-title">
                <h6 class="title">{{ __('Upcoming Payments')}}</h6>
            </div>
            <div class="card-tools">
                <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Pending Payments"></em>
            </div>
        </div>
        <div class="card-amount">
            <span class="amount">{{ number_format($totalAmount, 2) }}</span>
            <span class="currency">{{ __('KES')}}</span>
        </div>
        <div class="invest-data mt-1">
            <div class="invest-data-amount g-2">
                <div class="invest-data-history">
                    <div class="title">{{ __('Pending')}}</div>
                    <div class="amount">{{ $payments->count() }}</div>
                </div>
                <div class="invest-data-history">
                    <div class="title">{{ __('Next Due')}}</div>
                    <div class="amount">{{ $payments->first() ? $payments->first()->due_date->format('M d') : 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .card -->
