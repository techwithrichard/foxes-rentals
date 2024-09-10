<?php

namespace App\Http\Livewire\Admin\Expenses;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\House;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateExpenseComponent extends Component
{

    use WithFileUploads;

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
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:8024',

    ];


    public function mount()
    {
        $this->all_properties = Property::select('id', 'name', 'landlord_id')->get()->toArray();
        $this->landlord_properties = $this->all_properties;
    }


    public function render()
    {
        $categories = ExpenseType::pluck('name', 'id');
        $landlords = User::role('landlord')->get();
        return view('livewire.admin.expenses.create-expense-component',
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

    }

    //when a property is selected, show all houses belonging to that property
    public function updatedBuilding($value)
    {
        $this->houses = House::query()->where('property_id', $value)->pluck('name', 'id');
    }


    public function save()
    {
        $this->validate();

        //if file is uploaded, save it
        if ($this->attachment) {
            $fileToStore = Storage::url(Storage::putFile('public/documents', $this->attachment));
        }

        $expense = Expense::create([

            'amount' => $this->amount,
            'incurred_on' => $this->incurred_on,
            'description' => $this->expense_name,
            'receipt' => $fileToStore ?? null,
            'expense_type_id' => $this->expense_type_id,
            'landlord_id' => $this->landlord,
            'property_id' => $this->building,
            'house_id' => $this->unit,
            'notes' => $this->notes,
        ]);

        return redirect()->route('admin.expenses.index')
            ->with('success', __('Expense created successfully'));


    }

}
