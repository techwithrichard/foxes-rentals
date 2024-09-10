<?php

namespace App\Http\Livewire\Admin\House;

use App\Models\House;
use App\Models\Lease;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class DeleteHouseComponent extends Component
{

    use LivewireAlert;

    public $house_id;

    protected $listeners = ['deleteHouse'];

    public function render()
    {
        return view('livewire.admin.house.delete-house-component');
    }

    public function deleteHouse($id)
    {
        $this->house_id = $id;
        //emit event to show modal
        $this->emit('showDeleteModal');
    }

    public function submit()
    {

        //get lease associated with house and delete them
        $lease = Lease::where('house_id', $this->house_id)->first();
        $house = House::findOrFail($this->house_id);


        DB::transaction(
            function () use ($lease, $house) {
                $lease?->delete();
                $house->delete();
            }
        );

        $this->alert('success', __('House deleted successfully'));
        $this->emit('refreshTable');
        $this->reset('house_id');

    }
}
