@extends('admin.layouts.app')

@section('title', 'Add Manual Payment')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Add Manual Payment</h3>
            <div class="nk-block-des text-soft">
                <p>Manually record a payment that wasn't automatically processed</p>
            </div>
        </div>
        <div class="nk-block-head-content">
            <a href="{{ route('admin.payment-verification.index') }}" class="btn btn-outline-secondary">
                <em class="icon ni ni-arrow-left"></em>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </div>
</div>

<div class="nk-block">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.payment-verification.store') }}" method="POST" id="manual-payment-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">M-PESA Reference Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                           name="reference_number" value="{{ old('reference_number') }}" 
                                           placeholder="e.g., TIA8C983BA" required>
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                           name="amount" value="{{ old('amount') }}" 
                                           step="0.01" min="0.01" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required>
                                        <option value="">Select payment method...</option>
                                        <option value="MPESA STK" {{ old('payment_method') == 'MPESA STK' ? 'selected' : '' }}>M-PESA STK Push</option>
                                        <option value="MPESA C2B" {{ old('payment_method') == 'MPESA C2B' ? 'selected' : '' }}>M-PESA C2B</option>
                                        <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('paid_at') is-invalid @enderror" 
                                           name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}" required>
                                    @error('paid_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Tenant <span class="text-danger">*</span></label>
                            <select class="form-select @error('tenant_id') is-invalid @enderror" name="tenant_id" id="tenant-select" required>
                                <option value="">Select tenant...</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }} ({{ $tenant->phone }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Invoice (Optional)</label>
                            <select class="form-select @error('invoice_id') is-invalid @enderror" name="invoice_id" id="invoice-select">
                                <option value="">Select invoice (optional)...</option>
                            </select>
                            @error('invoice_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty if this is a general payment or overpayment</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3" 
                                      placeholder="Add any additional notes about this payment...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <em class="icon ni ni-save"></em>
                                Record Payment
                            </button>
                            <a href="{{ route('admin.payment-verification.index') }}" class="btn btn-outline-secondary ms-2">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Quick Reference</h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Common M-PESA Reference Formats:</h6>
                    <ul class="list-unstyled">
                        <li><code>TIA8C983BA</code> - STK Push</li>
                        <li><code>OGE123456789</code> - C2B Payment</li>
                        <li><code>QFT123456789</code> - Paybill</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="text-primary">Payment Verification Tips:</h6>
                    <ul class="list-unstyled small">
                        <li>• Always verify the reference number with the tenant</li>
                        <li>• Check the amount matches the invoice</li>
                        <li>• Select the correct invoice for reconciliation</li>
                        <li>• Add notes for future reference</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="text-primary">Overpayment Handling:</h6>
                    <p class="small text-muted mb-0">
                        If the payment amount exceeds the invoice amount, an overpayment record will be automatically created for future use.
                    </p>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title">Transaction Search</h6>
                </div>
                <div class="card-body">
                    <form id="quick-search-form">
                        <div class="form-group mb-3">
                            <label class="form-label">Search Reference</label>
                            <input type="text" class="form-control" id="quick-reference" placeholder="Enter reference number...">
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <em class="icon ni ni-search"></em>
                            Search
                        </button>
                    </form>
                    <div id="quick-search-results" class="mt-3" style="display: none;">
                        <!-- Search results will appear here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load tenant invoices when tenant is selected
    $('#tenant-select').on('change', function() {
        const tenantId = $(this).val();
        if (tenantId) {
            loadTenantInvoices(tenantId);
        } else {
            $('#invoice-select').empty().append('<option value="">Select invoice (optional)...</option>');
        }
    });
    
    // Quick search functionality
    $('#quick-search-form').on('submit', function(e) {
        e.preventDefault();
        const reference = $('#quick-reference').val().trim();
        if (reference) {
            quickSearchTransaction(reference);
        }
    });
    
    // Auto-fill form from search results
    $(document).on('click', '.use-search-result', function() {
        const data = $(this).data('payment');
        $('input[name="reference_number"]').val(data.reference);
        $('input[name="amount"]').val(data.amount);
        $('#quick-search-results').hide();
    });
});

function loadTenantInvoices(tenantId) {
    $.get('{{ route("admin.payment-verification.tenant-invoices") }}', { tenant_id: tenantId })
        .done(function(data) {
            const select = $('#invoice-select');
            select.empty().append('<option value="">Select invoice (optional)...</option>');
            
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

function quickSearchTransaction(reference) {
    $.post('{{ route("admin.payment-verification.search") }}', {
        reference_number: reference,
        _token: '{{ csrf_token() }}'
    })
    .done(function(data) {
        displayQuickSearchResults(data);
    })
    .fail(function() {
        $('#quick-search-results').html('<div class="alert alert-danger">Error searching for transaction</div>').show();
    });
}

function displayQuickSearchResults(data) {
    const resultsDiv = $('#quick-search-results');
    let html = '<div class="alert alert-info"><h6>Search Results</h6>';
    
    if (data.found) {
        html += '<ul class="list-unstyled mb-2">';
        
        if (data.stk_request) {
            html += `<li><strong>STK Request:</strong> Found - Status: ${data.stk_request.status}</li>`;
            html += `<li><strong>Amount:</strong> {{ setting("currency_symbol") }} ${parseFloat(data.stk_request.amount).toFixed(2)}</li>`;
            html += `<li><strong>Phone:</strong> ${data.stk_request.phone}</li>`;
            
            if (data.stk_request.status === 'Paid') {
                html += `<li><button class="btn btn-sm btn-outline-primary use-search-result" data-payment='${JSON.stringify({
                    reference: data.reference,
                    amount: data.stk_request.amount
                })}'>Use This Data</button></li>`;
            }
        }
        
        if (data.c2b_request) {
            html += `<li><strong>C2B Request:</strong> Found - Amount: {{ setting("currency_symbol") }} ${parseFloat(data.c2b_request.TransAmount).toFixed(2)}</li>`;
        }
        
        if (data.existing_payment) {
            html += '<li><strong>Payment Record:</strong> Already exists in system</li>';
        }
        
        html += '</ul>';
    } else {
        html += '<p class="mb-0">No records found for this reference number.</p>';
    }
    
    html += '</div>';
    resultsDiv.html(html).show();
}
</script>
@endpush

