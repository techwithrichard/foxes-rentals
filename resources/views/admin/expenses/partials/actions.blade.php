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
                        <a href="{{ route('admin.expenses.show',$expense->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{__('View Details')}}</span>
                        </a>
                    </li>
                    @can('edit expense')
                        <li>
                            <a href="{{ route('admin.expenses.edit',$expense->id) }}">
                                <em class="icon ni ni-edit"></em>
                                <span>{{__('Edit Expense')}}</span>
                            </a>
                        </li>
                    @endcan

                    @can('delete expense')
                        <li>
                            <a href="javascript:void(0);"
                               onclick="$('#delete-tenant-{{$expense->id}}').submit();">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Delete Expense')}}</span>
                            </a>

                            <form action="{{ route('admin.expenses.destroy',$expense->id) }}"
                                  id="delete-tenant-{{$expense->id}}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete expense ?')}}')"
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
