<div>
    <button type="button" class="btn btn-primary" wire:click="update" wire:loading.class="disabled" id="78787"
            data-redirect="no" wire:loading.attr="disabled">
        <div wire:loading wire:target="update">
            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true" wire:loading
                  wire:target="update"></span>
        </div>
        <span>{{ $slot}}</span>
    </button>
</div>
