<div class="card card-bordered card-full">
    <div class="card-inner">
        <div class="card-title-group">
            <div class="card-title">
                <h6 class="title">{{ __('Outstanding Payments')}}</h6>
            </div>
        </div>
    </div>

    @if($outstanding_payments->count()>0)
        <div class="nk-tb-list mt-n2">
            <div class="nk-tb-item nk-tb-head">
                <div class="nk-tb-col"><span>{{ __('Invoice No')}}.</span></div>
                <div class="nk-tb-col tb-col-sm"><span>{{ __('Customer')}}</span></div>
                <div class="nk-tb-col tb-col-md"><span>{{ __('Date')}}</span></div>
                <div class="nk-tb-col"><span>{{ __('Amount')}}</span></div>
                <div class="nk-tb-col"><span class="d-none d-sm-inline">{{ __('Status')}}</span></div>
            </div>

            @foreach($outstanding_payments as $invoice)
                <div class="nk-tb-item">
                    <div class="nk-tb-col">
                        <span class="tb-lead"><a href="{{ route('admin.rent-invoice.show',$invoice->id) }}">#{{ $invoice->invoice_id }}</a></span>
                    </div>
                    <div class="nk-tb-col tb-col-sm">
                        <div class="user-card">
                            <div class="user-avatar sm bg-purple-dim">
                                <span>{{ $invoice->tenant->initials }}</span>
                            </div>
                            <div class="user-name">
                                <span class="tb-lead">{{ $invoice->tenant->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="nk-tb-col tb-col-md">
                        <span class="tb-sub">{{ $invoice->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="nk-tb-col">
                    <span
                        class="tb-sub tb-amount">{{ number_format($invoice->balance_due) }} <span>{{ setting('currency_symbol') }}</span></span>
                    </div>
                    <div class="nk-tb-col">
                        @switch($invoice->status)
                            @case(\App\Enums\PaymentStatusEnum::PENDING)
                            <span class="badge badge-dot badge-dot-xs bg-danger">{{ __('Pending')}}</span>
                            @break
                            @case(\App\Enums\PaymentStatusEnum::PARTIALLY_PAID)
                            <span class="badge badge-dot badge-dot-xs bg-success">{{ __('Partially Paid')}}</span>
                            @break

                            @default

                            <span class="badge badge-dot badge-dot-xs bg-warning">{{ __('Due')}}</span>

                        @endswitch

                    </div>
                </div>
            @endforeach

        </div>

    @else
        <div class="example-alert px-3 px-2 mb-3">
            <div class="alert alert-gray alert-icon">
                <em class="icon ni ni-alert-circle"></em>
                <strong>{{ __('No outstanding payments available')}}</strong>
            </div>
        </div>
    @endif

</div>
