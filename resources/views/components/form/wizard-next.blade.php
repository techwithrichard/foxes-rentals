<div>
    <button type="button" class="btn btn-primary mr-1" wire:click="goToNextPage" wire:loading.class="disabled" id="{{ uniqid()}}"
        data-redirect="no" wire:loading.attr="disabled">
        <div wire:loading.delay wire:target="goToNextPage">
            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true" wire:loading
                wire:target="goToNextPage"></span>
        </div>
        <span>{{ $slot}}</span>
        <em class="icon ni ni-arrow-right" wire:loading.remove wire:target="goToNextPage"></em>
    </button>
</div>
