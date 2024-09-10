<?php

namespace App\Http\Livewire\Admin\Property;


use Livewire\Component;
use Spatie\LivewireWizard\Components\WizardComponent;

class CreatePropertyWizard extends WizardComponent
{

    public function steps(): array
    {
        return [
            PropertyDetailsStepComponent::class,
            PropertyExtrasStepComponent::class,
            PropertyAddressStepComponent::class,
//            PropertyUnitsStepComponent::class
        ];
    }
}
