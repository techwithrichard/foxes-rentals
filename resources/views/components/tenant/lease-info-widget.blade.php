<div class="card card-bordered card-full">
    <div class="card-inner">
        <div class="card-title-group align-start mb-0">
            <div class="card-title">
                <h6 class="title">{{ __('My Lease')}}</h6>
            </div>
            <div class="card-tools">
                <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Current Lease Information"></em>
            </div>
        </div>
        @if($hasActiveLease)
            <div class="card-amount">
                <span class="amount">{{ $leaseInfo->property->name ?? 'N/A' }}</span>
            </div>
            <div class="invest-data mt-1">
                <div class="invest-data-amount g-2">
                    <div class="invest-data-history">
                        <div class="title">{{ __('Rent')}}</div>
                        <div class="amount">{{ number_format($leaseInfo->rent, 2) }}</div>
                    </div>
                    <div class="invest-data-history">
                        <div class="title">{{ __('Status')}}</div>
                        <div class="amount">{{ ucfirst($leaseInfo->status) }}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="card-amount">
                <span class="amount">{{ __('No Active Lease')}}</span>
            </div>
            <div class="invest-data mt-1">
                <div class="invest-data-amount g-2">
                    <div class="invest-data-history">
                        <div class="title">{{ __('Status')}}</div>
                        <div class="amount">{{ __('Inactive')}}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div><!-- .card -->
