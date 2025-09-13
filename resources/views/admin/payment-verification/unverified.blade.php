@extends('admin.layouts.app')

@section('title', 'Unverified Payments')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Unverified Payments</h3>
            <div class="nk-block-des text-soft">
                <p>Payments that require manual verification and reconciliation</p>
            </div>
        </div>
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="{{ route('admin.payment-verification.create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em>
                                <span>Add Manual Payment</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.payment-verification.index') }}" class="btn btn-outline-secondary">
                                <em class="icon ni ni-arrow-left"></em>
                                <span>Back to Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nk-block">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="unverified-payments-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reference Number</th>
                            <th>Amount</th>
                            <th>Tenant</th>
                            <th>Phone</th>
                            <th>Property</th>
                            <th>House</th>
                            <th>Invoice Amount</th>
                            <th>Invoice Balance</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Verify Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verification-form">
                <div class="modal-body">
                    <input type="hidden" id="payment-id" name="payment_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="modal-reference" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Amount</label>
                                <input type="text" class="form-control" id="modal-amount" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tenant</label>
                                <input type="text" class="form-control" id="modal-tenant" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Payment Date</label>
                                <input type="text" class="form-control" id="modal-date" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Select Invoice to Reconcile <span class="text-danger">*</span></label>
                        <select class="form-select" id="invoice-select" name="invoice_id" required>
                            <option value="">Select an invoice...</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" id="verification-notes" name="notes" rows="3" placeholder="Add any notes about this verification..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <em class="icon ni ni-check"></em>
                        Verify & Reconcile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#unverified-payments-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.payment-verification.unverified") }}',
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'reference_number', name: 'reference_number' },
            { data: 'amount', name: 'amount' },
            { data: 'tenant_name', name: 'tenant_name' },
            { data: 'tenant_phone', name: 'tenant_phone' },
            { data: 'property_name', name: 'property_name' },
            { data: 'house_name', name: 'house_name' },
            { data: 'invoice_amount', name: 'invoice_amount' },
            { data: 'invoice_balance', name: 'invoice_balance' },
            { data: 'paid_at', name: 'paid_at' },
            { data: 'verification_status', name: 'verification_status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[9, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });

    // Handle verification form submission
    $('#verification-form').on('submit', function(e) {
        e.preventDefault();
        
        const paymentId = $('#payment-id').val();
        const formData = {
            invoice_id: $('#invoice-select').val(),
            notes: $('#verification-notes').val(),
            _token: '{{ csrf_token() }}'
        };
        
        $.post('{{ route("admin.payment-verification.verify", ":id") }}'.replace(':id', paymentId), formData)
            .done(function(response) {
                if (response.success) {
                    $('#verificationModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            })
            .fail(function() {
                showAlert('error', 'Error verifying payment. Please try again.');
            });
    });
});

function verifyPayment(paymentId, paymentData) {
    $('#payment-id').val(paymentId);
    $('#modal-reference').val(paymentData.reference_number);
    $('#modal-amount').val(paymentData.amount);
    $('#modal-tenant').val(paymentData.tenant_name);
    $('#modal-date').val(paymentData.paid_at);
    
    // Load tenant invoices
    loadTenantInvoices(paymentData.tenant_id);
    
    $('#verificationModal').modal('show');
}

function loadTenantInvoices(tenantId) {
    $.get('{{ route("admin.payment-verification.tenant-invoices") }}', { tenant_id: tenantId })
        .done(function(data) {
            const select = $('#invoice-select');
            select.empty().append('<option value="">Select an invoice...</option>');
            
            data.forEach(function(invoice) {
                select.append(`<option value="${invoice.id}">
                    Invoice #${invoice.invoice_id} - ${invoice.property_name} ${invoice.house_name} 
                    ({{ setting("currency_symbol") }} ${parseFloat(invoice.balance).toFixed(2)} balance)
                </option>`);
            });
        })
        .fail(function() {
            console.error('Failed to load tenant invoices');
        });
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    
    $('.nk-block').first().prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush

