<?php

namespace App\Http\Livewire\Admin\Tenants;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class EditTenantComponent extends Component
{

    public $tenant;

    public $name;
    public $email;
    public $phone;
    public $identity_no;
    public $identity_document;
    public $occupation_status;
    public $occupation_place;
    public $kin_name;
    public $kin_identity;
    public $kin_phone;
    public $kin_relationship;
    public $emergency_name;
    public $emergency_contact;
    public $emergency_email;
    public $emergency_relationship;
    public $address;

    public bool $shouldSendWelcomeEmail = false;

    protected $rules = [
        'name' => 'required',
        //email is not required because it is not editable


        'phone' => 'required',
        'identity_no' => 'required',
        'identity_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4048',
        'occupation_status' => 'nullable|string',
        'occupation_place' => 'nullable|string',
        'kin_name' => 'nullable|string',
        'kin_identity' => 'nullable|string',
        'kin_phone' => 'nullable|string',
        'kin_relationship' => 'nullable|string',
        'emergency_name' => 'nullable|string',
        'emergency_contact' => 'nullable|string',
        'emergency_email' => 'nullable|email',
        'emergency_relationship' => 'nullable|string',
        'address' => 'required',
    ];


    public function mount($tenant)
    {
        $this->tenant = $tenant;
        $this->name = $tenant->name;
        $this->email = $tenant->email;
        $this->phone = $tenant->phone;
        $this->identity_no = $tenant->identity_no;

        $this->occupation_status = $tenant->occupation_status;
        $this->occupation_place = $tenant->occupation_place;
        $this->kin_name = $tenant->kin_name;
        $this->kin_identity = $tenant->kin_identity;
        $this->kin_phone = $tenant->kin_phone;
        $this->kin_relationship = $tenant->kin_relationship;
        $this->emergency_name = $tenant->emergency_name;
        $this->emergency_contact = $tenant->emergency_contact;
        $this->emergency_email = $tenant->emergency_email;
        $this->emergency_relationship = $tenant->emergency_relationship;
        $this->address = $tenant->address;


    }

    public function render(): Factory|View|Application
    {
        return view('livewire.admin.tenants.edit-tenant-component');
    }

    public function submit()
    {
        $this->validate();
        //get reference to identity document already uploaded
        $old_document = $this->tenant->identity_document;
        $new_document = null;
        //upload identity document
        if ($this->identity_document) {
            $new_document = Storage::url(Storage::putFile('public/documents', $this->identity_document));
        }

        $updatedTenant = $this->tenant->update([
            'name' => $this->name,
//            'email' => $this->email,
            'phone' => $this->phone,
            'identity_no' => $this->identity_no,
            'identity_document' => $new_document ?? $old_document,
            'occupation_status' => $this->occupation_status,
            'occupation_place' => $this->occupation_place,
            'kin_name' => $this->kin_name,
            'kin_identity' => $this->kin_identity,
            'kin_phone' => $this->kin_phone,
            'kin_relationship' => $this->kin_relationship,
            'emergency_name' => $this->emergency_name,
            'emergency_contact' => $this->emergency_contact,
            'emergency_email' => $this->emergency_email,
            'emergency_relationship' => $this->emergency_relationship,
            'address' => $this->address,
            'password' => 1,
        ]);


        if ($this->shouldSendWelcomeEmail) {
            $expiresAt = now()->addDays(366);
            $this->tenant->sendWelcomeNotification($expiresAt);
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', __('Tenant details updated successfully'));

    }
}
