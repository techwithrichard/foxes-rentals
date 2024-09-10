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
                        <a href="{{ route('admin.mpesa-c2b-transactions.reconcile',$transaction->id) }}">
                            <em class="icon ni ni-tranx"></em>
                            <span>Reconcile Payment</span>
                        </a>
                    </li>

                    <li>
                        <a href="#" data-id="{{$transaction->id}}" class="delete-item">
                            <em class="icon ni ni-delete text-danger"></em>
                            <span class="text-danger">Remove Transaction</span>
                        </a>

                    </li>


                </ul>
            </div>
        </div>
    </li>
</ul>
