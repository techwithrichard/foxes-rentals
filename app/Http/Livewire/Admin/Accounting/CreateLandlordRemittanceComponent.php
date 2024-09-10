<?php

namespace App\Http\Livewire\Admin\Accounting;

use App\Models\LandlordRemittance;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateLandlordRemittanceComponent extends Component
{
    use WithFileUploads;

    public $paid_on, $landlord, $amount, $remarks, $attachment;
    public $payment_method, $payment_reference;
    public $period_from, $period_to;


    protected $rules = [
        'landlord' => 'required',
        'paid_on' => 'required|date',
        'period_from' => 'required|date',
        'period_to' => 'required|date',
        'amount' => 'required|numeric|gt:0',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:8024',
    ];

    public function render()
    {
        $landlords = User::role('landlord')->get();
        $payment_methods = PaymentMethod::pluck('name');
        return view('livewire.admin.accounting.create-landlord-remittance-component', compact('landlords', 'payment_methods'));
    }

    public function submit()
    {
        $this->validate();

        $fileToStore = $this->attachment ? Storage::url(Storage::put('public/documents', $this->attachment)) : null;

        LandlordRemittance::create([
            'landlord_id' => $this->landlord,
            'paid_on' => $this->paid_on,
            'period_from' => $this->period_from,
            'period_to' => $this->period_to,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'remarks' => $this->remarks,
            'payment_receipt' => $fileToStore,
        ]);

        // $month is from period_from and period_to

        $month = $this->period_from. ' - ' . $this->period_to;


        $data = [
            'amount' => $this->amount,
            'date' => $this->paid_on,
            'month' => $month,
        ];

        $user = User::find($this->landlord);
        $user->notify(new \App\Notifications\LandlordRemittanceCreatedNotification($data));

        return redirect()->route('admin.landlord-remittance.index')
            ->with('success', __('Landlord remittance created successfully.'));


    }
}
