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
                        <a href="{{ route('admin.leases.show',$lease->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Details')}}</span>
                        </a>
                    </li>


                    {{--                    <li>--}}
                    {{--                        <a href="javascript:void(0);"--}}
                    {{--                           onclick="$('#delete-proof-{{$lease->id}}').submit();">--}}
                    {{--                            <em class="icon ni ni-delete text-danger"></em>--}}
                    {{--                            <span class="text-danger">{{ __('Terminate Lease')}}</span>--}}
                    {{--                        </a>--}}

                    {{--                        <form action="{{ route('admin.leases.destroy',$lease->id) }}"--}}
                    {{--                              id="delete-proof-{{$lease->id}}"--}}
                    {{--                              onsubmit="return confirm('{{ __('Are you sure you want to terminate this lease ?')}}')"--}}
                    {{--                              method="POST">--}}
                    {{--                            @csrf--}}
                    {{--                            @method('DELETE')--}}
                    {{--                        </form>--}}
                    {{--                    </li>--}}

                </ul>
            </div>
        </div>
    </li>
</ul>
