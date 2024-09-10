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
                    <li><a href="{{ route('admin.payments-proof.show',$proof->id) }}">
                            <em class="icon ni ni-focus"></em>
                            <span>{{ __('Details')}}</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0);"
                           onclick="$('#delete-proof-{{$proof->id}}').submit();">
                            <em class="icon ni ni-delete text-danger"></em>
                            <span class="text-danger">{{ __('Delete Proof')}}</span>
                        </a>

                        <form action="{{ route('admin.payments-proof.destroy',$proof->id) }}"
                              id="delete-proof-{{$proof->id}}"
                              onsubmit="return confirm('{{ __('Are you sure you want to delete this payment proof?')}}')"
                              method="POST">
                            @csrf
                            @method('DELETE')
                        </form>
                    </li>

                </ul>
            </div>
        </div>
    </li>
</ul>
