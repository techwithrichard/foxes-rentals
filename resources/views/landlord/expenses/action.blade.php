<div class="nk-tb-actions gx-1">
    @if($expense->receipt)
        <a href="{{ url($expense->receipt) }}"
           download
           class="dropdown-toggle btn btn-icon btn-trigger">
            <em class="icon ni ni-download"></em>
        </a>
    @endif

</div>
