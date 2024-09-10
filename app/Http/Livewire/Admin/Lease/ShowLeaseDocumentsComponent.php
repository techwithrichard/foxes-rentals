<?php

namespace App\Http\Livewire\Admin\Lease;

use App\Models\LeaseDocument;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ShowLeaseDocumentsComponent extends Component
{
    use LivewireAlert;

    public $leaseId;

    public function render()
    {
        $documents = LeaseDocument::where('lease_id', $this->leaseId)->get();
        return view('livewire.admin.lease.show-lease-documents-component', compact('documents'));
    }

    public function deleteDocument($id)
    {
        $document = LeaseDocument::find($id);
        $document->delete();
        $this->alert('success', __('Document deleted successfully'));

    }
}
