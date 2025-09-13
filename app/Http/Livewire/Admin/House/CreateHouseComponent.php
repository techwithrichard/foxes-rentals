<?php

namespace App\Http\Livewire\Admin\House;

use App\Enums\HouseStatusEnum;
use App\Models\House;
use App\Models\HouseType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateHouseComponent extends Component
{

    public $name, $rent, $type, $property_id, $description, $landlord, $electricity_id, $commission;
    public $house_status;

    protected $rules = [


    ];

    protected function getRules(): array
    {
        return [
            'name' => 'required',
            'rent' => 'required|numeric|min:1',
            'type' => 'nullable|string',
            'property_id' => 'required',
            'description' => 'nullable|string',
            'landlord' => 'required',
            'commission' => 'required|numeric|min:0,max:100',
            'house_status' => ['required', Rule::in([0, 2])],
//            'electricity_id' => 'nullable|numeric',
        ];
    }

    public function mount()
    {
        $this->house_status = HouseStatusEnum::VACANT->value;
    }

    public function render(): Factory|View|Application
    {
        $types = HouseType::pluck('name');
        // Allow houses to be added to any property, not just multi-unit ones
        $properties = Property::pluck('name', 'id');
        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();
        return view('livewire.admin.house.create-house-component', compact('types', 'properties', 'landlords'));
    }

    //when property_id changes, get the landlord of the property
    public function updatedPropertyId($value)
    {
        $property = Property::find($value);
        $this->landlord = $property->landlord_id ?? null;
        $this->commission = $property->commission ?? null;
        $this->electricity_id = $property->electricity_id ?? null;
    }

    public function submit()
    {
        $this->validate();

        House::create([
            'name' => $this->name,
            'rent' => $this->rent,
            'type' => $this->type,
            'property_id' => $this->property_id,
            'description' => $this->description,
            'landlord_id' => $this->landlord,
            'electricity_id' => $this->electricity_id,
            'commission' => $this->commission,
        ]);

        return redirect()->route('admin.houses.index')
            ->with('success', __('House created successfully'));
    }
}
