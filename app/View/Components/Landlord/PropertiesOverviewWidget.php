<?php

namespace App\View\Components\Landlord;

use App\Models\Property;
use Illuminate\View\Component;

class PropertiesOverviewWidget extends Component
{
    public $properties;
    public $totalProperties;
    public $occupiedProperties;
    public $vacantProperties;

    public function __construct($properties = null)
    {
        $user = auth()->user();
        
        $this->properties = $properties ?? $user->properties()->with(['houses', 'leases'])->get();
        $this->totalProperties = $this->properties->count();
        $this->occupiedProperties = $this->properties->where('is_vacant', false)->count();
        $this->vacantProperties = $this->properties->where('is_vacant', true)->count();
    }

    public function render()
    {
        return view('components.landlord.properties-overview-widget');
    }
}
