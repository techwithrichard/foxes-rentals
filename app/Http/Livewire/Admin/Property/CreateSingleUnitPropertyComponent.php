<?php

namespace App\Http\Livewire\Admin\Property;

use App\Enums\PropertyStatusEnum;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateSingleUnitPropertyComponent extends Component
{
    public $address1, $address2, $city, $state, $zip, $country;
    public $propertyName, $type, $description, $landlord;
    public $rent, $electricity_id, $commission;

    protected $rules = [

        'rent' => 'required|numeric|gt:0',
        'electricity_id' => 'nullable|string|max:255',
        'commission' => 'required|numeric|min:0|max:100',
        'propertyName' => 'required',
        'type' => 'required',
        'description' => 'nullable|string',
        'landlord' => 'required',
        'address1' => 'required',
        'city' => 'required',
        'country' => 'required'
    ];

    public function render(): Factory|View|Application
    {

        $propertyTypes = PropertyType::orderBy('name')->pluck('name', 'name');
        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();
        return view('livewire.admin.property.create-single-unit-property-component', compact('propertyTypes', 'landlords'));
    }

    public function submit()
    {

        $this->validate();

        DB::beginTransaction();
        try {
            $property = Property::create([
                'name' => $this->propertyName,
                'type' => $this->type,
                'description' => $this->description,
                'is_multi_unit' => false,
                'rent' => $this->rent,
                'commission' => $this->commission,
                'electricity_id' => $this->electricity_id,
                'status' => PropertyStatusEnum::VACANT,
                'landlord_id' => $this->landlord,
            ]);
            $property->address()->create([
                'address_one' => $this->address1,
                'address_two' => $this->address2,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'zip' => $this->zip

            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());

            return null;
        }
        session()->flash('success', __('A single unit property has been created successfully.'));
        return redirect()->route('admin.properties.index');

    }
}
