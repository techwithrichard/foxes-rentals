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
                        <a href="{{ route('admin.tenants.show',$tenant->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{__('View Details')}}</span>
                        </a>
                    </li>
                    @can('create tenant')



                        <li>
                            <a href="{{ route('admin.tenants.edit',$tenant->id) }}">
                                <em class="icon ni ni-edit"></em>
                                <span>{{__('Edit Tenant')}}</span>
                            </a>
                        </li>

                    @endcan

                    @can('delete tenant')


                        <li>
                            <a href="javascript:void(0);"
                               onclick="$('#delete-tenant-{{$tenant->id}}').submit();">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Archive Tenant')}}</span>
                            </a>

                            <form action="{{ route('admin.tenants.destroy',$tenant->id) }}"
                                  id="delete-tenant-{{$tenant->id}}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to archive this tenant ?')}}')"
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
