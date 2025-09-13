<div class="btn-group" role="group">
    <button type="button" class="btn btn-sm btn-outline-primary" 
            onclick="verifyPayment('{{ $payment->id }}', {
                reference_number: '{{ $payment->reference_number }}',
                amount: '{{ $payment->amount }}',
                tenant_name: '{{ $payment->tenant?->name ?? 'N/A' }}',
                tenant_id: '{{ $payment->tenant_id }}',
                paid_at: '{{ $payment->paid_at?->format('d M Y H:i') }}'
            })">
        <em class="icon ni ni-check"></em>
        Verify
    </button>
    
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                data-bs-toggle="dropdown" aria-expanded="false">
            <em class="icon ni ni-more-h"></em>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="#" onclick="viewPaymentDetails('{{ $payment->id }}')">
                    <em class="icon ni ni-eye"></em>
                    View Details
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" onclick="searchTransactionReference('{{ $payment->reference_number }}')">
                    <em class="icon ni ni-search"></em>
                    Search Reference
                </a>
            </li>
            @if($payment->invoice_id)
                <li>
                    <a class="dropdown-item" href="{{ route('admin.rent-invoice.show', $payment->invoice_id) }}">
                        <em class="icon ni ni-file-text"></em>
                        View Invoice
                    </a>
                </li>
            @endif
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="#" onclick="deletePayment('{{ $payment->id }}')">
                    <em class="icon ni ni-trash"></em>
                    Delete Payment
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
function viewPaymentDetails(paymentId) {
    // Implement payment details modal
    alert('Payment details view - ID: ' + paymentId);
}

function searchTransactionReference(reference) {
    // Open search in new tab or modal
    window.open('{{ route("admin.mpesa-stk-transactions") }}?search=' + reference, '_blank');
}

function deletePayment(paymentId) {
    if (confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
        $.ajax({
            url: '{{ route("admin.payments.destroy", ":id") }}'.replace(':id', paymentId),
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#unverified-payments-table').DataTable().ajax.reload();
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Error deleting payment');
            }
        });
    }
}
</script>

