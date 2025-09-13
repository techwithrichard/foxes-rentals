<ul class="nk-tb-actions gx-1">
    <li>
        <div class="drodown">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                <em class="icon ni ni-more-h"></em>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <ul class="link-list-opt no-bdr">
                    <li>
                        <a href="javascript:void(0);" class="text-success" onclick="restoreHouse('{{ $house->id }}')">
                            <em class="icon ni ni-undo"></em>
                            <span>{{ __('Restore House')}}</span>
                        </a>
                    </li>
                    @can('permanently delete records')
                        @if(auth()->user()->hasRole('admin'))
                            <li>
                                <a href="javascript:void(0);" class="text-danger" onclick="permanentlyDeleteHouse('{{ $house->id }}')">
                                    <em class="icon ni ni-trash"></em>
                                    <span>{{ __('Permanently Delete')}}</span>
                                </a>
                            </li>
                        @endif
                    @endcan
                </ul>
            </div>
        </div>
    </li>
</ul>
