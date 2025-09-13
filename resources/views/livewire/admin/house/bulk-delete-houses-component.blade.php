<div>
    <!-- Bulk Delete Confirmation Modal -->
    <div class="modal fade" tabindex="-1" id="modalBulkDeleteHouses" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Bulk Delete Houses')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <em class="icon ni ni-alert-circle"></em>
                        <strong>{{ __('Warning!')}}</strong>
                        {{ __('This action will permanently delete :count selected houses and their associated leases. This action cannot be undone.', ['count' => $totalHouses])}}
                    </div>
                    
                    <p>{{ __('Are you sure you want to delete the selected houses?')}}</p>
                    
                    <div class="mt-3">
                        <strong>{{ __('Selected Houses:')}}</strong>
                        <ul class="list-unstyled mt-2">
                            @foreach($selectedHouses as $houseId)
                                <li class="text-muted">• {{ __('House ID: :id', ['id' => $houseId])}}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Cancel')}}
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="deleteSelected">
                        <em class="icon ni ni-trash"></em>
                        {{ __('Delete :count Houses', ['count' => $totalHouses])}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    @if($totalHouses > 0)
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ __(':count houses selected', ['count' => $totalHouses])}}</strong>
        </div>
        <div>
            <button type="button" class="btn btn-danger btn-sm" wire:click="confirmDelete">
                <em class="icon ni ni-trash"></em>
                {{ __('Delete Selected')}}
            </button>
            <button type="button" class="btn btn-secondary btn-sm ms-2" wire:click="$set('selectedHouses', [])">
                {{ __('Clear Selection')}}
            </button>
        </div>
    </div>
    @endif
</div>

<script>
    // Listen for Livewire events
    document.addEventListener('livewire:load', function () {
        Livewire.on('showBulkDeleteModal', () => {
            $('#modalBulkDeleteHouses').modal('show');
        });
        
        Livewire.on('refreshTable', () => {
            $('#modalBulkDeleteHouses').modal('hide');
        });
    });
</script>
