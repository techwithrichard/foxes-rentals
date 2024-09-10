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
                        <a href="{{ route('admin.deposits.show',$deposit->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Details')}}</span>
                        </a>
                    </li>

                    @can('refund deposit')
                        @if(!$deposit->refund_paid)
                            <li>
                                <a href="javascript:void(0);"
                                   onclick="window.livewire.emit('refundDeposit','{{$deposit->id}}')">
                                    <em class="icon ni ni-redo"></em>
                                    <span>{{ __('Refund Deposit')}}</span>
                                </a>
                            </li>

                        @endif
                    @endcan

                    @can('delete deposit')


                        <li>
                            <a href="javascript:void(0);"
                               onclick="$('#delete-deposit-{{$deposit->id}}').submit();">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Remove Deposit')}}</span>
                            </a>

                            <form action="{{ route('admin.deposits.destroy',$deposit->id) }}"
                                  id="delete-deposit-{{$deposit->id}}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this deposit?')}}')"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                            </form>
                        </li>

                    @endcan

                </ul>
            </div>
        </div>
    </li>
</ul>
