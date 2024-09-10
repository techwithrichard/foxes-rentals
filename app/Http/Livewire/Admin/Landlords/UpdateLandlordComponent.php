<?php

namespace App\Http\Livewire\Admin\Landlords;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateLandlordComponent extends Component
{
    use WithFileUploads;

    public $landlordId;

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
    public $address;

    public bool $isPasswordReset = false;
    public bool $shouldSendWelcomeEmail = false;


    public function getRules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->landlordId,
            'phone' => 'required',
            'identity_no' => 'required',
            'identity_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4048',
            'occupation_status' => 'nullable|string',
            'occupation_place' => 'nullable|string',
            'kin_name' => 'nullable|string',
            'kin_identity' => 'nullable|string',
            'kin_phone' => 'nullable|string',
            'kin_relationship' => 'nullable|string',
            'address' => 'required',
        ];
    }

    public function mount()
    {
        $landlord = User::findOrfail($this->landlordId);
        $this->fill([
            'name' => $landlord->name,
            'email' => $landlord->email,
            'phone' => $landlord->phone,
            'identity_no' => $landlord->identity_no,
            'occupation_status' => $landlord->occupation_status,
            'occupation_place' => $landlord->occupation_place,
            'kin_name' => $landlord->kin_name,
            'kin_identity' => $landlord->kin_identity,
            'kin_phone' => $landlord->kin_phone,
            'kin_relationship' => $landlord->kin_relationship,
            'address' => $landlord->address,

        ]);
        //isPasswordReset checks whether $landlord->password is encrypted or not
        $this->isPasswordReset = !Hash::check($landlord->password, $landlord->password);
    }

    public function render()
    {
        return view('livewire.admin.landlords.update-landlord-component');
    }

    public function submit()
    {
        $this->validate();

        if (!empty($this->identity_document)) {
            $identityDocumentToStore = Storage::url(Storage::put('public/documents', $this->identity_document));
        }


        $tenant = User::findOrfail($this->landlordId);

        try {
            $tenant->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'identity_no' => $this->identity_no,
                'identity_document' => $identityDocumentToStore ?? null,
                'occupation_status' => $this->occupation_status,
                'occupation_place' => $this->occupation_place,
                'kin_name' => $this->kin_name,
                'kin_identity' => $this->kin_identity,
                'kin_phone' => $this->kin_phone,
                'kin_relationship' => $this->kin_relationship,
                'address' => $this->address,
                'password' => 1,
            ]);


        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return null;
        }

        if ($this->shouldSendWelcomeEmail) {
            $expiresAt = now()->addDays(366);
            $tenant->sendWelcomeNotification($expiresAt);
        }

        return redirect()->route('admin.landlords.index')
            ->with('success', __('Landlord details updated successfully'));

    }
}
