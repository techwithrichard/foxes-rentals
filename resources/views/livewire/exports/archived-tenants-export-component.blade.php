<div>
    <x-button loading="{{  __('Exporting') }}" wire:click="submit"
              class="btn btn-white btn-dim btn-outline-light">
        <em class="d-none d-sm-inline icon ni ni-download"></em>
        <span>
                                                            <span class="d-none d-md-inline">
                                                                CSV
                                                            </span>
                                                            {{ __('Export')}}
                                                        </span>

    </x-button>
</div>

