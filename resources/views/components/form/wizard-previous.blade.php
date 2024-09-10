<div>
    <button type="button" class="btn btn-secondary mr-2" wire:click="goToPreviousPage" wire:loading.class="disabled" id="{{ uniqid()}}"
        data-redirect="no" wire:loading.attr="disabled">
        <div wire:loading.delay wire:target="goToPreviousPage">
            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true" wire:loading
                wire:target="goToPreviousPage"></span>
        </div>
        <em class="icon ni ni-arrow-left" wire:loading.remove wire:target="goToPreviousPage"></em>
        <span>{{ $slot}}</span>
    </button>
</div>
