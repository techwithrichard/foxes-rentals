<?php

namespace App\Http\Livewire\Admin\Property;

use App\Enums\PropertyStatusEnum;
use App\Models\HouseType;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\RequiredIf;
use Livewire\Component;

class CreateMultiUnitPropertyComponent extends Component
{

    public $address1, $address2, $city, $state, $zip, $country;
    public $propertyName, $property_type, $description, $property_landlord;
    public $property_electricity_id;


    public $baseRent, $baseType, $basePropertyId, $baseDescription, $commission, $electricity_id;
    public $landlord;

    public $numberOfHouses = 1;
    public $startNumber = 1;
    public $prefix = 'House ';
    public $suffix = '';

    public bool $shouldGenerateUnits = false;
    
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


    protected function getRules(): array
    {
        return [
            'propertyName' => 'required',
            'description' => 'nullable|string',
            'address1' => 'required',
            'city' => 'required',
            'country' => 'required',
            'baseRent' => [new RequiredIf($this->shouldGenerateUnits), 'nullable', 'numeric', 'gt:0'],
            'baseDescription' => ['nullable', 'string'],
            'numberOfHouses' => [new RequiredIf($this->shouldGenerateUnits), 'nullable', 'numeric', 'min:1'],
            'startNumber' => [new RequiredIf($this->shouldGenerateUnits), 'nullable', 'numeric', 'min:1'],
            'commission' => [new RequiredIf($this->shouldGenerateUnits), 'nullable', 'numeric', 'min:0', 'max:100'],
            'landlord' => [new RequiredIf($this->shouldGenerateUnits), 'nullable'],
            'electricity_id' => 'nullable|string',
            'prefix' => 'nullable|string',
            'suffix' => 'nullable|string',
        ];
    }

    protected function getMessages(): array
    {
        return [
            'baseRent.required' => __('The base rent is required'),
            'baseRent.numeric' => __('The base rent must be a number'),
            'baseRent.min' => __('The base rent must be at least 1'),
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
            $this->distributeHousesAcrossFloors();
        }
    }

    public function render(): Factory|View|Application
    {

        $types = HouseType::pluck('name');
        $propertyTypes = PropertyType::orderBy('name')->pluck('name', 'name');
        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();

        return view('livewire.admin.property.create-multi-unit-property-component',
            compact('propertyTypes', 'landlords', 'types')
        );
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
            $this->distributeHousesAcrossFloors();
        } else {
            // Reset floor-based options when switching away
            $this->sameHousesPerFloor = false;
            $this->housesPerFloor = 0;
            $this->useFloorRanges = false;
            $this->floorRanges = [['start_floor' => 1, 'end_floor' => 1, 'houses_per_floor' => 0]];
        }
    }
    
    public function updatedSameHousesPerFloor($value)
    {
        \Log::info("updatedSameHousesPerFloor called with value: " . ($value ? 'true' : 'false'));
        \Log::info("numberingScheme: {$this->numberingScheme}");
        
        if ($value && $this->numberingScheme === 'floor_based') {
            \Log::info("Setting useFloorRanges to false and calling distributeHousesAcrossFloors");
            $this->useFloorRanges = false;
            $this->distributeHousesAcrossFloors();
        } else {
            // If unchecked, clear the housesPerFloor value
            if (!$value) {
                $this->housesPerFloor = 0;
                \Log::info("Cleared housesPerFloor because sameHousesPerFloor was unchecked");
            }
            \Log::info("NOT calling distributeHousesAcrossFloors - conditions not met");
        }
    }
    
    public function updatedHousesPerFloor($value)
    {
        \Log::info("updatedHousesPerFloor called with value: {$value}");
        \Log::info("sameHousesPerFloor: " . ($this->sameHousesPerFloor ? 'true' : 'false'));
        \Log::info("numberingScheme: {$this->numberingScheme}");
        
        // Automatically check "sameHousesPerFloor" when user enters a value
        if ($value > 0 && $this->numberingScheme === 'floor_based') {
            $this->sameHousesPerFloor = true;
            \Log::info("Auto-checked sameHousesPerFloor because housesPerFloor is set to: {$value}");
        }
        
        if ($this->sameHousesPerFloor && $this->numberingScheme === 'floor_based') {
            \Log::info("Calling distributeHousesAcrossFloors from updatedHousesPerFloor");
            $this->distributeHousesAcrossFloors();
        } else {
            \Log::info("NOT calling distributeHousesAcrossFloors - conditions not met");
        }
    }
    
    public function updatedUseFloorRanges($value)
    {
        if ($value && $this->numberingScheme === 'floor_based') {
            $this->sameHousesPerFloor = false;
            $this->distributeHousesAcrossFloors();
        }
    }
    
    public function updatedFloorRanges($value)
    {
        if ($this->useFloorRanges && $this->numberingScheme === 'floor_based') {
            $this->distributeHousesAcrossFloors();
        }
    }
    
    public function updateFloorRange($index, $field, $value)
    {
        $this->floorRanges[$index][$field] = $value;
        if ($this->useFloorRanges && $this->numberingScheme === 'floor_based') {
            $this->distributeHousesAcrossFloors();
        }
    }
    
    public function updatedNumberOfFloors($value)
    {
        if ($this->numberingScheme === 'floor_based') {
            $this->initializeFloorConfig();
            $this->distributeHousesAcrossFloors();
        }
    }
    
    public function updatedGroundFloorLabel($value)
    {
        if ($this->numberingScheme === 'floor_based' && !empty($this->floorConfig)) {
            $this->floorConfig[0]['label'] = $value;
        }
    }
    
    public function initializeFloorConfig()
    {
        \Log::info("initializeFloorConfig called with numberOfFloors: {$this->numberOfFloors}");
        $this->floorConfig = [];
        for ($i = 0; $i < $this->numberOfFloors; $i++) {
            $floorLabel = $i === 0 ? $this->groundFloorLabel : chr(65 + $i - 1); // A, B, C, etc. for floors above ground
            $this->floorConfig[] = [
                'floor' => $i + 1,
                'label' => $floorLabel,
                'houses' => 0
            ];
        }
        \Log::info('Floor config initialized:', $this->floorConfig);
        // Automatically distribute houses after initializing floor config
        $this->distributeHousesAcrossFloors();
    }
    
    public function distributeHousesAcrossFloors()
    {
        if ($this->numberingScheme !== 'floor_based') {
            return;
        }
        
        // Ensure values are integers to prevent string division errors
        $numberOfHouses = (int) $this->numberOfHouses;
        $numberOfFloors = (int) $this->numberOfFloors;
        
        if ($numberOfFloors <= 0 || $numberOfHouses <= 0) {
            return; // Prevent division by zero or invalid values
        }
        
        \Log::info("Distribution method check:");
        \Log::info("useFloorRanges: " . ($this->useFloorRanges ? 'true' : 'false'));
        \Log::info("sameHousesPerFloor: " . ($this->sameHousesPerFloor ? 'true' : 'false'));
        \Log::info("housesPerFloor: {$this->housesPerFloor}");
        
        if ($this->useFloorRanges && !empty($this->floorRanges)) {
            // Use floor ranges configuration
            \Log::info("Using floor ranges configuration");
            foreach ($this->floorRanges as $range) {
                $startFloor = (int) $range['start_floor'];
                $endFloor = (int) $range['end_floor'];
                $housesPerFloor = (int) $range['houses_per_floor'];
                
                for ($floorNum = $startFloor; $floorNum <= $endFloor; $floorNum++) {
                    $floorIndex = $floorNum - 1; // Convert to 0-based index
                    if (isset($this->floorConfig[$floorIndex])) {
                        $this->floorConfig[$floorIndex]['houses'] = $housesPerFloor;
                    }
                }
            }
        } elseif ($this->sameHousesPerFloor && $this->housesPerFloor > 0) {
            // Use fixed houses per floor
            $housesPerFloor = (int) $this->housesPerFloor;
            \Log::info("Using same houses per floor: {$housesPerFloor}");
            foreach ($this->floorConfig as &$floor) {
                $floor['houses'] = $housesPerFloor;
            }
        } else {
            // Default: distribute houses evenly across floors
            \Log::info("Using default distribution: {$numberOfHouses} houses across {$numberOfFloors} floors");
            $housesPerFloor = floor($numberOfHouses / $numberOfFloors);
            $remainingHouses = $numberOfHouses % $numberOfFloors;
            
            foreach ($this->floorConfig as $index => &$floor) {
                $floor['houses'] = $housesPerFloor + ($index < $remainingHouses ? 1 : 0);
            }
        }
        
        // Calculate and update total houses based on floor configuration
        $this->calculateTotalHouses();
        
        // Debug: Log the floor configuration
        \Log::info('Floor Config after distribution:', $this->floorConfig);
    }
    
    public function calculateTotalHouses()
    {
        $totalHouses = 0;
        foreach ($this->floorConfig as $floor) {
            $totalHouses += (int) $floor['houses'];
        }
        $this->numberOfHouses = $totalHouses;
        \Log::info("Calculated total houses: {$totalHouses}");
    }
    
    public function updatedFloorConfig($value, $index)
    {
        \Log::info("Floor config updated for index {$index}: {$value}");
        $this->calculateTotalHouses();
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
                $floor['houses'] = 0;
            }
            
            // Apply floor ranges
            foreach ($this->floorRanges as $range) {
                $startFloor = $range['start_floor'] - 1; // Convert to 0-based index
                $endFloor = $range['end_floor'] - 1; // Convert to 0-based index
                $housesPerFloor = $range['houses_per_floor'];
                
                for ($i = $startFloor; $i <= $endFloor && $i < count($this->floorConfig); $i++) {
                    $this->floorConfig[$i]['houses'] = $housesPerFloor;
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
    
    public function generateHouseNames()
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
                \Log::info('Generating floor-based names. Floor config:', $this->floorConfig);
                foreach ($this->floorConfig as $floorIndex => $floor) {
                    \Log::info("Floor {$floorIndex}: {$floor['label']} with {$floor['houses']} houses");
                    for ($i = 0; $i < $floor['houses']; $i++) {
                        $names[] = $floor['label'] . ($i + 1);
                        $houseIndex++;
                    }
                }
                \Log::info('Generated names:', $names);
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

        $houses = [];
        if ($this->shouldGenerateUnits) {
            $houseNames = $this->generateHouseNames();
            for ($i = 0; $i < $this->numberOfHouses; $i++) {
                $houses[] = [
                    'name' => $houseNames[$i] ?? ($this->prefix . ($this->startNumber + $i) . $this->suffix),
                    'rent' => $this->baseRent,
                    'type' => $this->baseType,
                    'property_id' => $this->basePropertyId,
                    'description' => $this->baseDescription,
                    'commission' => $this->commission,
                    'electricity_id' => $this->electricity_id,
                    'landlord_id' => $this->landlord,
                    'is_vacant' => true,
                    'status' => 0, // VACANT status
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::beginTransaction();
        try {
            $property = Property::create([
                'name' => $this->propertyName,
                'type' => $this->property_type,
                'description' => $this->description,
                'is_multi_unit' => true,
                'is_vacant' => true, // Multi-unit properties should be marked as vacant
                'electricity_id' => $this->property_electricity_id,
                'status' => PropertyStatusEnum::MULTI_UNIT,
                'landlord_id' => $this->property_landlord,
                'commission' => 0,
            ]);
            $property->address()->create([
                'address_one' => $this->address1,
                'address_two' => $this->address2,
                'city' => $this->city,
                'state' => $this->state,
                'country' => $this->country,
                'zip' => $this->zip

            ]);

            if ($this->shouldGenerateUnits) {
                // Update property_id in houses array
                foreach ($houses as &$house) {
                    $house['property_id'] = $property->id;
                }
                $property->houses()->createMany($houses);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());

            return null;
        }
        if ($this->shouldGenerateUnits) {
            session()->flash('success', __('A multi unit property with ' . $this->numberOfHouses . ' houses has been created successfully.'));
        } else {
            session()->flash('success', __('A multi unit property has been created successfully. You can now add houses to it by clicking houses tab on the left menu'));
        }
        return redirect()->route('admin.properties.index');

    }
}
