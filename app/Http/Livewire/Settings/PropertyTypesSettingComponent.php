<?php

namespace App\Http\Livewire\Settings;

use App\Models\PropertyType;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PropertyTypesSettingComponent extends Component
{

    use LivewireAlert;

    public $typeId, $name;
    public $is_new = true;

    protected $listeners = ['resetFields'];


    protected $rules = [
        'name' => 'required',
    ];


    public function render()
    {
        $names = PropertyType::all();
        return view('livewire.settings.property-types-setting-component', compact('names'));
    }

    public function deleteMethod($id)
    {
        PropertyType::destroy($id);

    }

    public function updateMethod($id)
    {

        $this->is_new = false;
        $this->typeId = $id;
        $type = PropertyType::findOrFail($id);
        $this->name = $type->name;

        $this->emit('showNameModal');

    }

    public function submit()
    {
        $this->validate();

        PropertyType::updateOrCreate(['id' => $this->typeId], ['name' => $this->name]);

        $this->reset(['name']);
        $this->is_new = true;
        $this->alert('success', __('New property type has been added'));

        $this->emit('closeModal');
    }

    public function resetFields()
    {
        $this->reset('name', 'typeId');
        $this->is_new = true;
    }
}
