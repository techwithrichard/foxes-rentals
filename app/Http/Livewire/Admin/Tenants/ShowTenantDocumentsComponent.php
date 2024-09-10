<?php

namespace App\Http\Livewire\Admin\Tenants;

use App\Models\IdentityDocument;
use App\Models\LeaseDocument;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class ShowTenantDocumentsComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $tenantId;

    public $identity_documents = [];

    protected $rules = [
        'identity_documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4048',
    ];

    public function render()
    {
        $documents = IdentityDocument::where('tenant_id', $this->tenantId)->get();
        return view('livewire.admin.tenants.show-tenant-documents-component', compact('documents'));
    }

    public function deleteDocument($id)
    {
        $document = IdentityDocument::find($id);
        $document->delete();

        //try silently deleting the file
        @unlink($document->path);

        $this->alert('success', 'Document deleted successfully');

    }

    public function submit()
    {
        $this->validate();

        //also make sure that identity documents is not empty,if empty,thow the error to the error bag
        if (empty($this->identity_documents)) {
            $this->addError('identity_documents', __('Please select at least one document'));
            return;
        }


        $identities = [];
        foreach ($this->identity_documents as $document) {
            $identities[] = [
                'name' => $document->getClientOriginalName(),
                'path' => Storage::url(Storage::putFile('public/documents', $document))
            ];

        }

        $tenant = \App\Models\User::findOrFail($this->tenantId);
        $tenant->identities()->createMany($identities);

        $this->alert('success', __('Documents uploaded successfully'));

        $this->reset('identity_documents');

        $this->dispatchBrowserEvent('file-pond-clear', ['id' => $this->id]);

    }
}
