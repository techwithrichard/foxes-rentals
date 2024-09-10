@props(['loading'=>false])

<div>
    <button type="button" class="btn btn-primary" wire:click="submit" wire:loading.class="disabled" id="{{ uniqid()}}"
            data-redirect="no" wire:loading.attr="disabled">

            <span class="spinner-border spinner-border-sm hide me-2" role="status" aria-hidden="true" wire:loading
                  wire:target="submit"></span>

            @if($loading)
                SOME LOADING
            @endif

        <span>{{ $slot}}</span>
    </button>
</div>
