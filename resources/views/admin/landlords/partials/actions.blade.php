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
                        <a href="{{ route('admin.landlords.show',$landlord->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Details')}}</span>
                        </a>
                    </li>
                    @can('edit landlord')
                        <li>
                            <a href="{{ route('admin.landlords.edit',$landlord->id) }}">
                                <em class="icon ni ni-edit"></em>
                                <span>{{ __('Edit Landlord')}}</span>
                            </a>
                        </li>
                    @endcan

                    @can('delete landlord')
                        <li>
                            <a href="javascript:void(0);"
                               onclick="$('#delete-landlord-{{$landlord->id}}').submit();">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Delete Landlord')}}</span>
                            </a>

                            <form action="{{ route('admin.landlords.destroy',$landlord->id) }}"
                                  id="delete-landlord-{{$landlord->id}}"
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this landlord ?')}}')"
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
