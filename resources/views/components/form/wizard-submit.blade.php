<div>
    <button type="button" class="btn btn-success mr-1" wire:click="submit" wire:loading.class="disabled"
        id="{{ uniqid()}}" data-redirect="no" wire:loading.attr="disabled">
        <div wire:loading wire:target="submit">
            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true" wire:loading
                wire:target="submit"></span>
        </div>
        <em class="icon ni ni-check-thick" wire:loading.remove wire:target="submit"></em>
        <span>{{ $slot}}</span>
    </button>
</div>
