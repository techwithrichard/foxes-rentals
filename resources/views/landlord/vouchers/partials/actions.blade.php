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
                        <a href="{{ route('landlord.vouchers.show',$voucher->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Voucher')}}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('landlord.vouchers.print',$voucher->id) }}"><em
                                class="icon ni ni-printer"></em>
                            <span>{{ __('Print Voucher')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </li>
</ul>
