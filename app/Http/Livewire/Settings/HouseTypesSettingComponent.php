<?php

namespace App\Http\Livewire\Settings;

use App\Models\HouseType;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class HouseTypesSettingComponent extends Component
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
        $names = HouseType::all();
        return view('livewire.settings.house-types-setting-component', compact('names'));
    }

    public function deleteMethod($id)
    {
        HouseType::destroy($id);

    }

    public function updateMethod($id)
    {

        $this->is_new = false;
        $this->typeId = $id;
        $type = HouseType::findOrFail($id);
        $this->name = $type->name;

        $this->emit('showNameModal');

    }

    public function submit()
    {
        $this->validate();

        HouseType::updateOrCreate(['id' => $this->typeId], ['name' => $this->name]);

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
