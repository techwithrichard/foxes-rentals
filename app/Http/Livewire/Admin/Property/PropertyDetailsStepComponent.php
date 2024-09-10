<?php

namespace App\Http\Livewire\Admin\Property;

use App\Models\PropertyType;
use App\Models\User;
use Livewire\Component;
use Spatie\LivewireWizard\Components\StepComponent;

class PropertyDetailsStepComponent extends StepComponent
{

    public $propertyName, $type, $description, $landlord;
    public bool $is_single_unit = false;

    protected $rules = [
        'propertyName' => 'required',
        'type' => 'required',
        'description' => 'nullable|string',
        'landlord' => 'required',
        'is_single_unit' => 'required|boolean',

    ];

    public function render()
    {
        $propertyTypes = PropertyType::orderBy('name')->pluck('name', 'name');
        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();
        return view('livewire.admin.property.property-details-step-component',
            ['propertyTypes' => $propertyTypes, 'landlords' => $landlords]);
    }

    public function stepInfo(): array
    {
        return [
            'label' => __('Add Details'),
            'description' => __('Add details to your property')
        ];
    }

    public function submit()
    {
        $this->validate();
        $this->nextStep();

//        if ($this->is_single_unit) {
//            $this->nextStep();
//        } else {
//
//            $this->showStep('property-address-step');
//        }

    }
}

