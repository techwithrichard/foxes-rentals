<?php

namespace App\Http\Livewire\Admin\Property;

use App\Models\House;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyUnitsTableComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $propertyId;

    public function render()
    {

        $units = House::where('property_id', $this->propertyId)
            ->with('lease.tenant')
            ->paginate(10);
        return view('livewire.admin.property.property-units-table-component', ['units' => $units]);
    }
}
