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

    public function render(): Factory|View|Application
    {

        $types = HouseType::pluck('name');
        $propertyTypes = PropertyType::orderBy('name')->pluck('name', 'name');
        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();

        return view('livewire.admin.property.create-multi-unit-property-component',
            compact('propertyTypes', 'landlords', 'types')
        );
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
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::beginTransaction();
        try {
            $property = Property::create([
                'name' => $this->propertyName,
                'type' => $this->property_type,
                'description' => $this->description,
                'is_multi_unit' => true,
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
                $property->houses()->createMany($houses);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());

            return null;
        }
        session()->flash('success', __('A multi unit property has been created successfully.You can now add houses to it by clicking houses tab on the left menu'));
        return redirect()->route('admin.properties.index');

    }
}
