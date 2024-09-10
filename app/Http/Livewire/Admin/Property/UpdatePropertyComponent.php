<?php

namespace App\Http\Livewire\Admin\Property;

use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UpdatePropertyComponent extends Component
{

    public $propertyId;

    public $propertyName, $type, $description, $landlord;
    public $address1, $address2, $city, $state, $zip, $country;
    public $rent, $electricity_id, $commission;


    public function mount()
    {
        $property = Property::with('address')->findOrFail($this->propertyId);
        $this->fill([
            'propertyName' => $property->name,
            'type' => $property->type,
            'description' => $property->description,
            'landlord' => $property->landlord_id,
            'address1' => $property->address->address_one,
            'address2' => $property->address->address_two,
            'city' => $property->address->city,
            'state' => $property->address->state,
            'zip' => $property->address->zip,
            'country' => $property->address->country,
            'rent' => $property->rent,
            'electricity_id' => $property->electricity_id,
            'commission' => $property->commission,
        ]);

    }

    protected $rules = [
        'propertyName' => 'required',
        'type' => 'required',
        'description' => 'nullable|string',
        'landlord' => 'required',
        'address1' => 'required',
        'city' => 'required',
        'country' => 'required',
        'rent' => 'nullable|numeric',
        'electricity_id' => 'nullable|string|max:255',
        'commission' => 'required|numeric|min:0|max:100',
    ];

    public function render()
    {
        $propertyTypes = PropertyType::orderBy('name')->pluck('name', 'name');
        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();
        return view('livewire.admin.property.update-property-component',
            compact('propertyTypes', 'landlords')
        );
    }


    public function save()
    {
        $this->validate();
        $property = Property::with('address')->findOrFail($this->propertyId);

        //wrap in transaction
        DB::beginTransaction();

        try {
            $property->update([
                'name' => $this->propertyName,
                'type' => $this->type,
                'description' => $this->description,
                'landlord_id' => $this->landlord,
                'rent' => $this->rent,
                'electricity_id' => $this->electricity_id,
                'commission' => $this->commission,
            ]);
            $property->address->update([
                'address_one' => $this->address1,
                'address_two' => $this->address2,
                'city' => $this->city,
                'state' => $this->state,
                'zip' => $this->zip,
                'country' => $this->country,
            ]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());

            return null;
        }
        session()->flash('success', __('Property updated successfully'));
        return redirect()->route('admin.properties.index');


    }
}
