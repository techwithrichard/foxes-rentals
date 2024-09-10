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

                    @can('delete overpayment')
                        <li>
                            <a href="javascript:void(0);"
                               onclick="$('#delete-proof-{{$overpayment->id}}').submit();">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Delete Overpayment')}}</span>
                            </a>

                            <form action="{{ route('admin.overpayments.destroy',$overpayment->id) }}"
                                  id="delete-proof-{{$overpayment->id}}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this overpayment?')}}')"
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
