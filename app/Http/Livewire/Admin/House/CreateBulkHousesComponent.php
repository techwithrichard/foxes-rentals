<?php

namespace App\Http\Livewire\Admin\House;

use App\Models\House;
use App\Models\HouseType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateBulkHousesComponent extends Component
{
    public $baseRent, $baseType, $basePropertyId, $baseDescription, $commission, $electricity_id;
    public  $landlord;

    public $numberOfHouses = 1;
    public $startNumber = 1;
    public $prefix = 'House ';
    public $suffix = '';

    protected $rules = [
        'baseRent' => 'required|numeric|min:1',
        'baseType' => 'nullable|string',
        'basePropertyId' => 'required',
        'baseDescription' => 'nullable|string',
        'numberOfHouses' => 'required|numeric|min:1',
        'startNumber' => 'required|numeric|min:1',
        'commission' => 'required|numeric|min:0,max:100',
        'landlord' => 'required',
        'electricity_id' => 'nullable|string',
        'prefix' => 'nullable|string',
        'suffix' => 'nullable|string',
    ];

    protected function getMessages(): array
    {
        return [
            'baseRent.required' => __('The base rent is required'),
            'baseRent.numeric' => __('The base rent must be a number'),
            'baseRent.min' => __('The base rent must be at least 1'),
            'basePropertyId.required' => __('The main property is required'),
            'basePropertyId.numeric' => __('The main property must be a number'),
            'basePropertyId.min' => __('The main property must be at least 1'),
            'numberOfHouses.required' => __('The number of houses is required'),
            'numberOfHouses.numeric' => __('The number of houses must be a number'),
            'numberOfHouses.min' => __('The number of houses must be at least 1'),
            'startNumber.required' => __('The start number is required'),
            'startNumber.numeric' => __('The start number must be a number'),
            'startNumber.min' => __('The start number must be at least 1'),
        ];
    }

    public function render()
    {
        $types = HouseType::pluck('name');
        $properties = Property::where('is_multi_unit', 1)->pluck('name', 'id');
        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();
        return view('livewire.admin.house.create-bulk-houses-component',
            compact('types', 'properties', 'landlords')
        );
    }

    public function updatedBasePropertyId($value)
    {
        $property = Property::find($value);
        $this->landlord = $property->landlord_id ?? null;
        $this->commission = $property->commission ?? null;
        $this->electricity_id = $property->electricity_id ?? null;
    }


    public function submit()
    {
        $this->validate();

        $houses = [];
        for ($i = 0; $i < $this->numberOfHouses; $i++) {
            $houses[] = [
                'name' => $this->prefix . ($this->startNumber + $i) . $this->suffix,
                'rent' => $this->baseRent,
                'type' => $this->baseType,
                'property_id' => $this->basePropertyId,
                'description' => $this->baseDescription,
                'commission' => $this->commission,
                'electricity_id' => $this->electricity_id,
                'landlord_id' => $this->landlord,
//                'created_at' => now(),
//                'updated_at' => now(),
            ];
        }

        $successMessage = $this->numberOfHouses . ' ' . __('houses has been created successfully.');

        DB::beginTransaction();
        try {
//            House::insert($houses);

            // create houses one by one to trigger the observer
            foreach ($houses as $house) {
                House::create($house);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return null;
        }


        return redirect()->route('admin.houses.index')
            ->with('success', $successMessage);


    }
}
