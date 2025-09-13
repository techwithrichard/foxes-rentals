<?php

namespace App\Http\Livewire\Admin\House;

use App\Models\House;
use App\Models\Lease;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class BulkDeleteHousesComponent extends Component
{
    use LivewireAlert;

    public $selectedHouses = [];
    public $selectAll = false;
    public $totalHouses = 0;

    protected $listeners = ['refreshTable', 'updateSelectedHouses'];

    public function render()
    {
        return view('livewire.admin.house.bulk-delete-houses-component');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Get all house IDs from the current page
            $this->emit('selectAllHouses');
        } else {
            $this->selectedHouses = [];
            $this->emit('deselectAllHouses');
        }
    }

    public function updateSelectedHouses($houses)
    {
        $this->selectedHouses = $houses;
        $this->totalHouses = count($houses);
    }

    public function deleteSelected()
    {
        if (empty($this->selectedHouses)) {
            $this->alert('warning', __('Please select houses to delete'));
            return;
        }

        // Get houses with their leases
        $houses = House::with('lease')->whereIn('id', $this->selectedHouses)->get();
        
        $deletedCount = 0;
        
        DB::transaction(function () use ($houses, &$deletedCount) {
            foreach ($houses as $house) {
                // Soft delete associated lease if exists
                if ($house->lease) {
                    $house->lease->delete();
                }
                
                // Soft delete the house
                $house->delete();
                $deletedCount++;
            }
        });

        $this->alert('success', __(':count houses moved to deleted records successfully', ['count' => $deletedCount]));
        $this->emit('refreshTable');
        $this->reset(['selectedHouses', 'selectAll', 'totalHouses']);
    }

    public function confirmDelete()
    {
        if (empty($this->selectedHouses)) {
            $this->alert('warning', __('Please select houses to delete'));
            return;
        }

        $this->emit('showBulkDeleteModal');
    }

    public function refreshTable()
    {
        // This method is called when the table needs to be refreshed
        // The actual refresh is handled by JavaScript in the view
        $this->emit('refreshTable');
    }
}
