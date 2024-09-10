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
                        <a href="{{ route('admin.vouchers.show',$voucher->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Voucher')}}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.vouchers.print',$voucher->id) }}" download target="_blank"><em
                                class="icon ni ni-printer"></em>
                            <span>{{ __('Print Voucher')}}</span>
                        </a>
                    </li>

                    @can('delete landlord voucher')


                        <li>
                            <a href="javascript:void(0);"
                               onclick="$('#delete-tenant-{{$voucher->id}}').submit();">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Delete Voucher')}}</span>
                            </a>

                            <form action="{{ route('admin.vouchers.destroy',$voucher->id) }}"
                                  id="delete-tenant-{{$voucher->id}}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete voucher ?')}}')"
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
