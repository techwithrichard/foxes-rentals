<ul class="nk-tb-actions gx-1">
    <li>
        <div class="drodown">
            <a href="#"
               class="dropdown-toggle btn btn-icon btn-trigger"
               data-bs-toggle="dropdown"><em
                    class="icon ni ni-more-h"></em></a>
            <div
                class="dropdown-menu dropdown-menu-end">
                <ul class="link-list-opt no-bdr">

                    <li>
                        <a href="{{ route('tenant.invoices.show',$invoice->id) }}" target="_blank">

                            <em class="icon ni ni-printer"></em>
                            <span>{{ __('Download Invoice')}}</span>
                        </a>
                    </li>

                    @if($invoice->status == \App\Enums\PaymentStatusEnum::PENDING || $invoice->status == \App\Enums\PaymentStatusEnum::PARTIALLY_PAID)
                        <li>
                            <a href="{{ route('tenant.initiate_mpesa_payment',$invoice->id) }}"
                               onclick="return confirm('You will be receive prompt to pay via MPESA. Are you sure you want to continue?')">
                                <em class="icon ni ni-paypal-alt"></em>
                                <span>Pay With MPESA</span>
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </li>
</ul>
