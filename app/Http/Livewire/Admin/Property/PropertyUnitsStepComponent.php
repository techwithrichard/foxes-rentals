<?php

namespace App\Http\Livewire\Admin\Property;

use App\Enums\PropertyStatusEnum;
use App\Models\HouseType;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\LivewireWizard\Components\StepComponent;

class PropertyUnitsStepComponent extends StepComponent
{

    public $baseRent, $baseType, $basePropertyId, $baseDescription;

    public $numberOfHouses = 1;
    public $startNumber = 1;
    public $prefix = 'House ';
    public $suffix = '';

    protected $rules = [
        'baseRent' => 'required|numeric|min:1',
        'baseType' => 'nullable|string',
        'baseDescription' => 'nullable|string',
        'numberOfHouses' => 'required|numeric|min:1',
        'startNumber' => 'required|numeric|min:1',
        'prefix' => 'nullable|string',
        'suffix' => 'nullable|string',
    ];

    protected function getMessages(): array
    {
        return [
            'baseRent.required' => 'The base rent is required',
            'baseRent.numeric' => 'The base rent must be a number',
            'baseRent.min' => 'The base rent must be at least 1',
            'numberOfHouses.required' => 'The number of houses is required',
            'numberOfHouses.numeric' => 'The number of houses must be a number',
            'numberOfHouses.min' => 'The number of houses must be at least 1',
            'startNumber.required' => 'The start number is required',
            'startNumber.numeric' => 'The start number must be a number',
            'startNumber.min' => 'The start number must be at least 1',
        ];
    }


    public function mount()
    {

    }

    public function render()
    {
        $types = HouseType::pluck('name');
        return view('livewire.admin.property.property-units-step-component', compact('types'));
    }


    public function stepInfo(): array
    {
        return [
            'label' => __('Add Units'),
            'description' => __('Add units to your property')
        ];

    }

    public function submit()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            //create property
            $property = Property::create([
                'name' => $this->state()->forStep('property-details-step')['propertyName'],
                'type' => $this->state()->forStep('property-details-step')['type'],
                'description' => $this->state()->forStep('property-details-step')['description'],
                'is_multi_unit' => true,
                'status' => PropertyStatusEnum::MULTI_UNIT,
                'landlord_id' => $this->state()->forStep('property-details-step')['landlord'],
                'commission' => $this->state()->forStep('property-details-step')['commission'],
            ]);

            //create address
            $property->address()->create([
                'address_one' => $this->state()->forStep('property-address-step')['address1'],
                'address_two' => $this->state()->forStep('property-address-step')['address2'],
                'city' => $this->state()->forStep('property-address-step')['city'],
                'state' => $this->state()->forStep('property-address-step')['state'],
                'country' => $this->state()->forStep('property-address-step')['country'],
                'zip' => $this->state()->forStep('property-address-step')['zip'],
            ]);

            //create units
            for ($i = 0; $i < $this->numberOfHouses; $i++) {
                $property->houses()->create([
                    'name' => $this->prefix . ($i + $this->startNumber) . $this->suffix,
                    'rent' => $this->baseRent,
                    'type' => $this->baseType,
                    'description' => $this->baseDescription,
                    'landlord_id' => $this->state()->forStep('property-details-step')['landlord'],
                    'commission' => $this->state()->forStep('property-details-step')['commission'],
                ]);
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return null;
        }

        return redirect()->route('admin.properties.index')
            ->with('success', __('Property and units created successfully'));


    }
}

