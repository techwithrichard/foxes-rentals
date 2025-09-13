@extends('admin.layouts.app')

@section('title', 'Payment Verification')

@section('content')
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Payment Verification Dashboard</h3>
            <div class="nk-block-des text-soft">
                <p>Manage and verify payments that weren't automatically processed</p>
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
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nk-block">
    <!-- Statistics Cards -->
    <div class="row g-gs">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <em class="icon ni ni-credit-card text-primary fs-2x"></em>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Payments</h6>
                            <div class="h4 mb-0" id="total-payments">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <em class="icon ni ni-check-circle text-success fs-2x"></em>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Verified Payments</h6>
                            <div class="h4 mb-0" id="verified-payments">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <em class="icon ni ni-alert-circle text-warning fs-2x"></em>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Unverified Payments</h6>
                            <div class="h4 mb-0" id="unverified-payments">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <em class="icon ni ni-money text-info fs-2x"></em>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Overpayments</h6>
                            <div class="h4 mb-0" id="total-overpayments">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-gs mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Quick Actions</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.payment-verification.unverified') }}" class="btn btn-outline-warning">
                            <em class="icon ni ni-eye"></em>
                            View Unverified Payments
                        </a>
                        <a href="{{ route('admin.payment-verification.create') }}" class="btn btn-outline-primary">
                            <em class="icon ni ni-plus"></em>
                            Add Manual Payment
                        </a>
                        <a href="{{ route('admin.mpesa-stk-transactions') }}" class="btn btn-outline-info">
                            <em class="icon ni ni-list"></em>
                            STK Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Transaction Search</h6>
                    <form id="transaction-search-form">
                        <div class="input-group">
                            <input type="text" class="form-control" id="reference-number" placeholder="Enter M-PESA reference number...">
                            <button class="btn btn-primary" type="submit">
                                <em class="icon ni ni-search"></em>
                                Search
                            </button>
                        </div>
                    </form>
                    <div id="search-results" class="mt-3" style="display: none;">
                        <!-- Search results will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Unverified Payments -->
    <div class="row g-gs mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Recent Unverified Payments</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="recent-unverified-table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Tenant</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
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
    // Load statistics
    loadStatistics();
    
    // Transaction search
    $('#transaction-search-form').on('submit', function(e) {
        e.preventDefault();
        const referenceNumber = $('#reference-number').val().trim();
        
        if (!referenceNumber) {
            alert('Please enter a reference number');
            return;
        }
        
        searchTransaction(referenceNumber);
    });
    
    // Load recent unverified payments
    loadRecentUnverifiedPayments();
});

function loadStatistics() {
    $.get('{{ route("admin.payment-verification.statistics") }}')
        .done(function(data) {
            $('#total-payments').text(data.total_payments);
            $('#verified-payments').text(data.verified_payments);
            $('#unverified-payments').text(data.unverified_payments);
            $('#total-overpayments').text('{{ setting("currency_symbol") }} ' + parseFloat(data.total_overpayments).toFixed(2));
        })
        .fail(function() {
            console.error('Failed to load statistics');
        });
}

function searchTransaction(referenceNumber) {
    $.post('{{ route("admin.payment-verification.search") }}', {
        reference_number: referenceNumber,
        _token: '{{ csrf_token() }}'
    })
    .done(function(data) {
        displaySearchResults(data);
    })
    .fail(function() {
        alert('Error searching for transaction');
    });
}

function displaySearchResults(data) {
    const resultsDiv = $('#search-results');
    let html = '<div class="alert alert-info"><h6>Search Results for: ' + data.reference + '</h6>';
    
    if (data.found) {
        html += '<ul class="list-unstyled mb-0">';
        
        if (data.stk_request) {
            html += '<li><strong>STK Request:</strong> Found - Status: ' + data.stk_request.status + '</li>';
        }
        
        if (data.c2b_request) {
            html += '<li><strong>C2B Request:</strong> Found - Amount: {{ setting("currency_symbol") }} ' + data.c2b_request.TransAmount + '</li>';
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

function loadRecentUnverifiedPayments() {
    // This would typically load via DataTables, but for simplicity showing static data
    // In a real implementation, you'd use DataTables with AJAX
}
</script>
@endpush

