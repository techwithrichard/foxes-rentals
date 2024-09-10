<?php

namespace App\Http\Livewire\Admin\House;

use App\Models\House;
use App\Models\HouseType;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpdateHouseComponent extends Component
{
    public $houseId;

    public $name, $rent, $type, $description, $status;
    public $house;
    public $landlord;
    public $commission;

    protected $rules = [
        'name' => 'required',
        'rent' => 'required|numeric|min:1',
        'type' => 'nullable|string',
        'description' => 'nullable|string',
        'commission' => 'required|numeric|min:0|max:100',
        'landlord' => 'required',
        'status' => 'required|numeric',
    ];

    public function mount()
    {
        $this->house = House::findOrFail($this->houseId);
        //fill the form with the current values
        $this->name = $this->house->name;
        $this->rent = $this->house->rent;
        $this->type = $this->house->type;
        $this->description = $this->house->description;
        $this->commission = $this->house->commission;
        $this->landlord = $this->house->landlord_id;
        $this->status = $this->house->status;


    }

    public function render(): Factory|View|Application
    {
        $types = HouseType::pluck('name');

        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();
        return view('livewire.admin.house.update-house-component', compact('types', 'landlords'));
    }

    public function submit()
    {
        $this->validate();

        $this->house->update([
            'name' => $this->name,
            'rent' => $this->rent,
            'type' => $this->type,
            'description' => $this->description,
            'commission' => $this->commission,
            'landlord_id' => $this->landlord,
            'status' => $this->status,
        ]);

        return redirect()->route('admin.houses.index')
            ->with('success', __('House updated successfully'));

    }
}
