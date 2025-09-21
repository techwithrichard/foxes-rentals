<div class="card card-bordered card-full">
    <div class="card-inner">
        <div class="card-title-group align-start mb-0">
            <div class="card-title">
                <h6 class="title">{{ __('Recent Payments')}}</h6>
            </div>
            <div class="card-tools">
                <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Recent Payment Activity"></em>
            </div>
        </div>
        <div class="card-amount">
            <span class="amount">{{ number_format($totalAmount, 2) }}</span>
            <span class="currency">{{ __('KES')}}</span>
        </div>
        <div class="invest-data mt-1">
            <div class="invest-data-amount g-2">
                <div class="invest-data-history">
                    <div class="title">{{ __('Total Payments')}}</div>
                    <div class="amount">{{ $payments->count() }}</div>
                </div>
                <div class="invest-data-history">
                    <div class="title">{{ __('This Month')}}</div>
                    <div class="amount">{{ $payments->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div><!-- .card -->
