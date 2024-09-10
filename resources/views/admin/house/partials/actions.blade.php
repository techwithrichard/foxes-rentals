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
                        <a href="{{ route('admin.houses.show',$house->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Details')}}</span>
                        </a>
                    </li>

                    @can('edit house')
                        <li>
                            <a href="{{ route('admin.houses.edit',$house->id) }}">
                                <em class="icon ni ni-edit-alt-fill"></em>
                                <span>{{ __('Update House')}}</span>
                            </a>
                        </li>
                    @endcan

                    @can('delete house')
                        <li>
                            <a href="javascript:void(0);" class="text-danger"
                               onclick="window.livewire.emit('deleteHouse','{{$house->id}}')">
                                <em class="icon ni ni-trash"></em>
                                <span>{{ __('Delete House')}}</span>
                            </a>
                        </li>
                    @endcan

                </ul>
            </div>
        </div>
    </li>
</ul>
