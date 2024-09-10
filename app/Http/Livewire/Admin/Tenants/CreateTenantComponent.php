<?php

namespace App\Http\Livewire\Admin\Tenants;

use App\Rules\MinWordsRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\Pure;
use Livewire\Component;
use Livewire\WithFileUploads;


class CreateTenantComponent extends Component
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
    public $emergency_name;
    public $emergency_contact;
    public $emergency_email;
    public $emergency_relationship;
    public $address;

    public bool $shouldSendWelcomeEmail = false;

    public $identity_documents = [];

    #[Pure] protected function getRules(): array
    {
        return [
            'name' => ['required', new MinWordsRule(2)],
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:9|max:12|regex:/^\S*$/',
            'identity_no' => 'required',
            'identity_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4048',
            'identity_documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4048',
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
    }

    protected function getMessages(): array
    {
        return [
            'phone.regex' => 'No spaces are allowed in the phone number',
        ];
    }

    public function render()
    {
        return view('livewire.admin.tenants.create-tenant-component');
    }

    public function submit()
    {
        $this->validate();

        //initialize $tenant


        if (!empty($this->identity_document)) {
            $identityDocumentToStore = Storage::url(Storage::putFile('public/documents', $this->identity_document));
        }

        $identities = [];
        if (!empty($this->identity_documents)) {

            foreach ($this->identity_documents as $document) {
                $identities[] = [
                    'name' => $document->getClientOriginalName(),
                    'path' => Storage::url(Storage::putFile('public/documents', $document))
                ];
            }
        }


        DB::beginTransaction();

        try {
            $tenant = \App\Models\User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => '254' . $this->phone,
                'identity_no' => $this->identity_no,
                'identity_document' => $identityDocumentToStore ?? null,
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

            $tenant->identities()->createMany($identities);

            $tenant->assignRole('tenant');

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

        return redirect()->route('admin.tenants.index')
            ->with('success', __('Tenant created successfully.Proceed to assign a room to the tenant'));

    }
}
