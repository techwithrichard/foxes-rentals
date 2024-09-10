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
                        <a href="{{ route('admin.properties.show',$property->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Details')}}</span>
                        </a>
                    </li>

                    @can('edit property')

                        <li>
                            <a href="{{ route('admin.properties.edit',$property->id) }}">
                                <em class="icon ni ni-edit"></em>
                                <span>{{ __('Edit Property')}}</span>
                            </a>
                        </li>

                    @endcan

                    @can('delete property')
                        <li>
                            <a href="#" data-id="{{$property->id}}" class="delete-item">
                                <em class="icon ni ni-delete text-danger"></em>
                                <span class="text-danger">{{ __('Delete Property')}}</span>
                            </a>

                        </li>
                    @endcan

                </ul>
            </div>
        </div>
    </li>
</ul>
