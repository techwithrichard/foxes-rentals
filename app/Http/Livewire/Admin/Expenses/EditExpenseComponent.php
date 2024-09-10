<?php

namespace App\Http\Livewire\Admin\Expenses;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\House;
use App\Models\Property;
use App\Models\User;
use Livewire\Component;

class EditExpenseComponent extends Component
{

    public $expenseId;

    public $expense_type_id, $expense_name;
    public $amount, $incurred_on, $landlord, $building, $unit;
    public $billable_to_landlord, $notes;
    public $attachment;

    public $all_properties = [];
    public $landlord_properties = [];
    public $houses = [];

    protected $rules = [
        'expense_name' => 'required',
        'amount' => 'required|numeric|min:0',
        'incurred_on' => 'required|date',
        'landlord' => 'nullable|exists:users,id',
    ];


    public function mount()
    {


        $expense = Expense::findOrFail($this->expenseId);
        $this->fill([
            'expense_type_id' => $expense->expense_type_id,
            'expense_name' => $expense->description,
            'amount' => $expense->amount,
            'incurred_on' => $expense->incurred_on->format('Y-m-d'),
            'landlord' => $expense->landlord_id,
            'building' => $expense->property_id ?? null,
            'unit' => $expense->house_id ?? null,
            'notes' => $expense->notes,

        ]);

        $this->all_properties = Property::select('id', 'name', 'landlord_id')->get()->toArray();
        $this->landlord_properties = $this->all_properties;


    }

    public function render()
    {
        $categories = ExpenseType::pluck('name', 'id');
        $landlords = User::role('landlord')->get();
        return view('livewire.admin.expenses.edit-expense-component',
            [
                'categories' => $categories,
                'landlords' => $landlords
            ]
        );
    }

    public function updatedLandlord($value)
    {
        if ($value == null) {
            $this->landlord_properties = $this->all_properties;
        } else {
            $this->landlord_properties = array_filter($this->all_properties, function ($property) use ($value) {
                return $property['landlord_id'] == $value;
            });
        }
        $this->houses = [];
        $this->reset('building', 'unit');

    }

    public function updatedBuilding($value)
    {
        $this->houses = House::query()->where('property_id', $value)->pluck('name', 'id');
    }

    public function save()
    {
        $this->validate();

        $expense = Expense::findOrFail($this->expenseId);

        $expense->update([
            'expense_type_id' => $this->expense_type_id,
            'description' => $this->expense_name,
            'amount' => $this->amount,
            'incurred_on' => $this->incurred_on,
            'landlord_id' => $this->landlord ?? null,
            'property_id' => $this->building ?? null,
            'house_id' => $this->unit ?? null,
            'notes' => $this->notes ?? null,
        ]);

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', __('Expense updated successfully'));
    }
}
