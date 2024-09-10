<div>
    <button type="button" class="btn btn-mw btn-danger" wire:click="submit" wire:loading.class="disabled" id="{{ uniqid()}}"
            data-redirect="no" wire:loading.attr="disabled">
        <div wire:loading wire:target="submit">
            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true" wire:loading
                  wire:target="submit"></span>
        </div>
        <span>{{ $slot}}</span>
    </button>
</div>
