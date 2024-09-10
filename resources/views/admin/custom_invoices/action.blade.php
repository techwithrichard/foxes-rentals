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
                    @can('view custom invoice')
                        <li>
                            <a href="{{ route('admin.custom-invoice.show',$invoice->id) }}">
                                <em class="icon ni ni-eye"></em>
                                <span>{{ __('View Invoice')}}</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.custom-invoice.print',$invoice->id) }}" download><em
                                    class="icon ni ni-printer" ></em>
                                <span>{{ __('Print Invoice')}}</span>
                            </a>
                        </li>
                    @endcan

                    @can('delete custom invoice')



                        <li>
                            <a href="javascript:void(0);"
                               onclick="$('#delete-tenant-{{$invoice->id}}').submit();">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Delete Invoice')}}</span>
                            </a>

                            <form action="{{ route('admin.custom-invoice.destroy',$invoice->id) }}"
                                  id="delete-tenant-{{$invoice->id}}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete landlord invoice ?')}}')"
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
