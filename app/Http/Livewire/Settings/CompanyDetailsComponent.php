<?php

namespace App\Http\Livewire\Settings;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CompanyDetailsComponent extends Component
{
    use LivewireAlert;

    public $company_name;
    public $company_address;
    public $company_phone;
    public $company_email;

    protected $rules = [
        'company_name' => 'required',
        'company_address' => 'required',
        'company_phone' => 'nullable|string',
        'company_email' => 'nullable|email',
    ];


    public function mount()
    {
        $this->company_name = setting('company_name');
        $this->company_address = setting('company_address');
        $this->company_phone = setting('company_phone');
        $this->company_email = setting('company_email');


    }

    public function render()
    {
        return view('livewire.settings.company-details-component');
    }

    public function submit()
    {

        $this->validate();

        //set the settings
        setting(['company_name' => $this->company_name]);
        setting(['company_address' => $this->company_address]);
        setting(['company_phone' => $this->company_phone]);
        setting(['company_email' => $this->company_email]);

        //save the settings
        setting()->save();
        $this->alert('success', __('Company Details Updated Successfully'));
    }
}
