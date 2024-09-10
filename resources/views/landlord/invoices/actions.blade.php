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
                        <a href="{{ route('landlord.invoices.show',$invoice->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Invoice')}}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('landlord.invoices.print',$invoice->id) }}"><em
                                class="icon ni ni-printer"></em>
                            <span>{{ __('Print Invoice')}}</span>
                        </a>
                    </li>


                </ul>
            </div>
        </div>
    </li>
</ul>
