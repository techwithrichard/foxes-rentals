<?php

namespace App\Http\Livewire\Settings;

use App\Models\ExpenseType;
use App\Models\PropertyType;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ExpenseTypesSettingComponent extends Component
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
        $names = ExpenseType::all();
        return view('livewire.settings.expense-types-setting-component', compact('names'));
    }

    public function deleteMethod($id)
    {
        ExpenseType::destroy($id);
    }

    public function updateMethod($id)
    {

        $this->is_new = false;
        $this->typeId = $id;
        $type = ExpenseType::findOrFail($id);
        $this->name = $type->name;

        $this->emit('showNameModal');

    }

    public function submit()
    {
        $this->validate();

        ExpenseType::updateOrCreate(['id' => $this->typeId], ['name' => $this->name]);

        $this->reset(['name']);
        $this->is_new = true;
        $this->alert('success', __('Expense category has been added'));

        $this->emit('closeModal');
    }

    public function resetFields()
    {
        $this->reset('name', 'typeId');
        $this->is_new = true;
    }


}
