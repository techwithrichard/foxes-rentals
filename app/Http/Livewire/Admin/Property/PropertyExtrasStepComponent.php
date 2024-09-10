<?php

namespace App\Http\Livewire\Admin\Property;

use Livewire\Component;
use Spatie\LivewireWizard\Components\StepComponent;

class PropertyExtrasStepComponent extends StepComponent
{

    public $rent, $electricity_id, $commission;

    protected $rules = [
        'rent' => 'nullable|numeric',
        'electricity_id' => 'nullable|string|max:255',
        'commission' => 'required|numeric|min:0|max:100',
    ];

    public function render()
    {
        return view('livewire.admin.property.property-extras-step-component');
    }

    public function stepInfo(): array
    {
        return [
            'label' => __('Add Extras'),
            'description' => __('Add rent and electricity details')
        ];
    }

    public function submit()
    {
        $this->validate();
        $this->nextStep();

    }
}
