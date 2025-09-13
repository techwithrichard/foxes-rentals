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
    
    // Numbering scheme options
    public $numberingScheme = 'simple'; // 'simple', 'floor_based', 'alphabetical', 'numeric_sequence', 'custom'
    
    // Floor-based labeling options
    public $numberOfFloors = 1;
    public $groundFloorLabel = 'G';
    public $floorConfig = [];
    public $sameHousesPerFloor = false;
    public $housesPerFloor = 0;
    public $useFloorRanges = false;
    public $floorRanges = [
        ['start_floor' => 1, 'end_floor' => 1, 'houses_per_floor' => 0]
    ];
    
    // Alphabetical numbering options
    public $alphabeticalStart = 'A';
    public $alphabeticalEnd = 'Z';
    
    // Custom numbering options
    public $customPrefixes = [];
    public $customSuffixes = [];

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
    
    public function mount()
    {
        // Initialize floor config if using floor-based numbering
        if ($this->numberingScheme === 'floor_based') {
            $this->initializeFloorConfig();
        }
    }

    public function render()
    {
        $types = HouseType::pluck('name');
        // Allow houses to be added to any property, not just multi-unit ones
        $properties = Property::pluck('name', 'id');
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
    
    public function updatedNumberOfHouses($value)
    {
        // Auto-calculate floor distribution when using floor-based numbering
        if ($this->numberingScheme === 'floor_based') {
            $this->distributeHousesAcrossFloors();
        }
    }
    
    public function updatedNumberingScheme($value)
    {
        // Reset floor config when switching schemes
        if ($value === 'floor_based') {
            $this->initializeFloorConfig();
        } else {
            // Reset floor-based options when switching away
            $this->sameHousesPerFloor = false;
            $this->housesPerFloor = 0;
            $this->useFloorRanges = false;
            $this->floorRanges = [['start_floor' => 1, 'end_floor' => 1, 'houses_per_floor' => 0]];
        }
    }
    
    public function updatedNumberOfFloors($value)
    {
        if ($this->numberingScheme === 'floor_based') {
            $this->initializeFloorConfig();
        }
    }
    
    public function updatedGroundFloorLabel($value)
    {
        if ($this->numberingScheme === 'floor_based' && !empty($this->floorConfig)) {
            $this->floorConfig[0]['prefix'] = $value;
        }
    }
    
    public function updatedSameHousesPerFloor($value)
    {
        if ($this->numberingScheme === 'floor_based' && $value) {
            // Uncheck the other option
            $this->useFloorRanges = false;
            
            if (!empty($this->floorConfig)) {
                // Apply the same number to all floors
                foreach ($this->floorConfig as $index => &$floor) {
                    $floor['houses_per_floor'] = $this->housesPerFloor;
                }
            }
        }
    }
    
    public function updatedHousesPerFloor($value)
    {
        if ($this->numberingScheme === 'floor_based' && $this->sameHousesPerFloor && !empty($this->floorConfig)) {
            // Apply the same number to all floors
            foreach ($this->floorConfig as $index => &$floor) {
                $floor['houses_per_floor'] = $value;
            }
        }
    }
    
    public function updatedUseFloorRanges($value)
    {
        if ($this->numberingScheme === 'floor_based' && $value) {
            // Uncheck the other option
            $this->sameHousesPerFloor = false;
            $this->initializeFloorRanges();
        }
    }
    
    public function addFloorRange()
    {
        $this->floorRanges[] = [
            'start_floor' => count($this->floorRanges) + 1,
            'end_floor' => count($this->floorRanges) + 1,
            'houses_per_floor' => 0
        ];
    }
    
    public function removeFloorRange($index)
    {
        if (count($this->floorRanges) > 1) {
            unset($this->floorRanges[$index]);
            $this->floorRanges = array_values($this->floorRanges);
        }
    }
    
    public function applyFloorRanges()
    {
        if ($this->numberingScheme === 'floor_based' && $this->useFloorRanges && !empty($this->floorConfig)) {
            // Reset all floors to 0
            foreach ($this->floorConfig as $index => &$floor) {
                $floor['houses_per_floor'] = 0;
            }
            
            // Apply floor ranges
            foreach ($this->floorRanges as $range) {
                $startFloor = $range['start_floor'] - 1; // Convert to 0-based index
                $endFloor = $range['end_floor'] - 1; // Convert to 0-based index
                $housesPerFloor = $range['houses_per_floor'];
                
                for ($i = $startFloor; $i <= $endFloor && $i < count($this->floorConfig); $i++) {
                    $this->floorConfig[$i]['houses_per_floor'] = $housesPerFloor;
                }
            }
        }
    }
    
    private function initializeFloorRanges()
    {
        $this->floorRanges = [
            ['start_floor' => 1, 'end_floor' => $this->numberOfFloors, 'houses_per_floor' => 0]
        ];
    }
    
    private function initializeFloorConfig()
    {
        $this->floorConfig = [];
        
        // Ground floor
        $this->floorConfig[] = [
            'floor_name' => 'Ground Floor',
            'prefix' => $this->groundFloorLabel,
            'houses_per_floor' => 0
        ];
        
        // Upper floors
        $floorNames = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth'];
        $floorPrefixes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        
        for ($i = 1; $i < $this->numberOfFloors; $i++) {
            $this->floorConfig[] = [
                'floor_name' => $floorNames[$i - 1] . ' Floor',
                'prefix' => $floorPrefixes[$i - 1],
                'houses_per_floor' => 0
            ];
        }
    }
    
    private function distributeHousesAcrossFloors()
    {
        $totalHouses = $this->numberOfHouses;
        $activeFloors = collect($this->floorConfig)->filter(function($floor) {
            return $floor['houses_per_floor'] > 0;
        });
        
        if ($activeFloors->isEmpty() && !empty($this->floorConfig)) {
            // Default distribution: equal houses per floor
            $housesPerFloor = floor($totalHouses / count($this->floorConfig));
            $remaining = $totalHouses % count($this->floorConfig);
            
            foreach ($this->floorConfig as $index => &$floor) {
                $floor['houses_per_floor'] = $housesPerFloor + ($index < $remaining ? 1 : 0);
            }
        }
    }
    
    private function generateHouseNames()
    {
        $names = [];
        
        switch ($this->numberingScheme) {
            case 'simple':
                for ($i = 0; $i < $this->numberOfHouses; $i++) {
                    $names[] = $this->prefix . ($this->startNumber + $i) . $this->suffix;
                }
                break;
                
            case 'floor_based':
                $houseIndex = 0;
                foreach ($this->floorConfig as $floor) {
                    if ($floor['houses_per_floor'] > 0) {
                        for ($i = 1; $i <= $floor['houses_per_floor']; $i++) {
                            $names[] = $floor['prefix'] . $i;
                            $houseIndex++;
                        }
                    }
                }
                break;
                
            case 'alphabetical':
                $alphabet = range($this->alphabeticalStart, $this->alphabeticalEnd);
                for ($i = 0; $i < $this->numberOfHouses; $i++) {
                    $names[] = $alphabet[$i % count($alphabet)] . ($i >= count($alphabet) ? floor($i / count($alphabet)) + 1 : '');
                }
                break;
                
            case 'numeric_sequence':
                for ($i = 0; $i < $this->numberOfHouses; $i++) {
                    $names[] = 'Unit ' . str_pad($this->startNumber + $i, 3, '0', STR_PAD_LEFT);
                }
                break;
                
            case 'custom':
                for ($i = 0; $i < $this->numberOfHouses; $i++) {
                    $prefix = $this->customPrefixes[$i] ?? 'Unit';
                    $suffix = $this->customSuffixes[$i] ?? ($i + 1);
                    $names[] = $prefix . ' ' . $suffix;
                }
                break;
        }
        
        return $names;
    }


    public function submit()
    {
        $this->validate();

        $houseNames = $this->generateHouseNames();
        $houses = [];
        
        for ($i = 0; $i < $this->numberOfHouses; $i++) {
            $houses[] = [
                'name' => $houseNames[$i],
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

    public function deleteHousesByScheme()
    {
        if (!$this->basePropertyId) {
            $this->alert('warning', __('Please select a property first'));
            return;
        }

        $this->emit('showDeleteBySchemeModal');
    }

    public function confirmDeleteByScheme()
    {
        if (!$this->basePropertyId) {
            $this->alert('warning', __('Please select a property first'));
            return;
        }

        // Get houses from the selected property
        $houses = House::where('property_id', $this->basePropertyId)->get();
        
        if ($houses->isEmpty()) {
            $this->alert('warning', __('No houses found for the selected property'));
            return;
        }

        DB::beginTransaction();
        try {
            // Soft delete associated leases first
            foreach ($houses as $house) {
                if ($house->lease) {
                    $house->lease->delete();
                }
            }

            // Soft delete all houses from the property
            $deletedCount = House::where('property_id', $this->basePropertyId)->delete();

            DB::commit();
            $this->alert('success', __(':count houses moved to deleted records successfully', ['count' => $deletedCount]));
            $this->emit('refreshTable');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', __('Error moving houses to deleted records: :message', ['message' => $e->getMessage()]));
        }
    }
}
