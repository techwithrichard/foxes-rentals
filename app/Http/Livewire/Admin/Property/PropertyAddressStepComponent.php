<?php

namespace App\Http\Livewire\Admin\Property;

use App\Enums\PropertyStatusEnum;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\LivewireWizard\Components\StepComponent;

class PropertyAddressStepComponent extends StepComponent
{
    public $address1, $address2, $city, $state, $zip, $country;

    protected $rules = [
        'address1' => 'required',
        'city' => 'required',
        'country' => 'required'
    ];

    public function render()
    {
        return view('livewire.admin.property.property-address-step-component');
    }

    public function stepInfo(): array
    {
        return [
            'label' => __('Add Address'),
            'description' => __('Add address to your property')
        ];
    }

    public function submit()
    {
        $this->validate();


        DB::beginTransaction();
        try {


            $property = Property::create([
                'name' => $this->state()->forStep('property-details-step')['propertyName'],
                'type' => $this->state()->forStep('property-details-step')['type'],
                'description' => $this->state()->forStep('property-details-step')['description'],
                'is_multi_unit' => !$this->state()->forStep('property-details-step')['is_single_unit'],
                'rent' => $this->state()->forStep('property-extras-step')['rent'],
                'commission' => $this->state()->forStep('property-extras-step')['commission'],
                'electricity_id' => $this->state()->forStep('property-extras-step')['electricity_id'],
                'status' => PropertyStatusEnum::VACANT,
                'landlord_id' => $this->state()->forStep('property-details-step')['landlord'],
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
        session()->flash('success', __('Proceed to add units,lease to tenant or manage important details.'));
        return redirect()->route('admin.properties.index');


    }

    public function previousStep()
    {
        //if property is multi unit, go back to property details step, else go back to property extras step
        if (!$this->state()->forStep('property-details-step')['is_single_unit']) {
            $this->showStep('property-details-step');
        } else {
            $this->showStep('property-extras-step');
        }


    }
}

