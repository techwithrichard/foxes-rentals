<ul class="nk-tb-actions gx-1">
    <li>
        <div class="drodown">
            <a href="#"
               class="dropdown-toggle btn btn-icon btn-trigger"
               data-bs-toggle="dropdown"><em
                    class="icon ni ni-more-h"></em>
            </a>


            <div
                class="dropdown-menu dropdown-menu-end">
                <ul class="link-list-opt no-bdr">
                    @if($payment->status=='pending')
                        <li>

                            <a href="javascript:void(0);"
                               onclick="window.livewire.emit('rejectPayment','{{$payment->id}}')">
                                <em class="icon ni ni-cross-circle text-danger"></em>
                                <span>{{ __('Reject Payment')}}</span>
                            </a>

                        </li>
                        <li>
                            <a href="javascript:void(0);"
                               onclick="window.livewire.emit('approvePayment','{{$payment->id}}')">
                                <em class="icon ni ni-check-circle text-success"></em>
                                <span>{{ __('Approve Payment')}}</span>
                            </a>
                        </li>
                    @endif

                        <li>
                            <a href="javascript:void(0);"
                               onclick="deletePayment({{$payment->id}})">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Delete Payment')}}</span>
                            </a>
                        </li>

                </ul>
            </div>


        </div>
    </li>
</ul>
