<?php

namespace App\Http\Livewire\Admin\Landlords;

use App\Rules\MinWordsRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateLandlordComponent extends Component
{
    use WithFileUploads;

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

    public bool $shouldSendWelcomeEmail = false;

    protected $rules = [


    ];


    protected function getRules()
    {
        return [
            'name' => ['required', new MinWordsRule(2)],
            'email' => 'required|email|unique:users',
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

    public function render()
    {
        return view('livewire.admin.landlords.create-landlord-component');
    }

    public function submit()
    {
        $this->validate();

        if (!empty($this->identity_document)) {
            $identityDocumentToStore = Storage::url(Storage::put('public/documents', $this->identity_document));
        }


        DB::beginTransaction();

        try {
            $tenant = \App\Models\User::create([
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

            $tenant->assignRole('landlord');

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return null;
        }

        if ($this->shouldSendWelcomeEmail) {
            $expiresAt = now()->addDays(366);
            $tenant->sendWelcomeNotification($expiresAt);
        }

        return redirect()->route('admin.landlords.index')
            ->with('success', __('Landlord account created successfully.They will receive an email to set up their password.'));

    }
}
