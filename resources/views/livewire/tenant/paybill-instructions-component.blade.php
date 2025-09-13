<div class="card card-bordered">
    <div class="card-inner">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">Paybill Payment Instructions</h5>
                <p class="nk-block-des">Use these details to pay via M-PESA Paybill</p>
            </div>
        </div>
        
        <div class="nk-block">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Paybill Number</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $paybillNumber }}" readonly>
                            <button class="btn btn-outline-primary" type="button" 
                                    wire:click="copyToClipboard('{{ $paybillNumber }}', 'Paybill Number')">
                                <em class="icon ni ni-copy"></em>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Account Number</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $accountNumber }}" readonly>
                            <button class="btn btn-outline-primary" type="button" 
                                    wire:click="copyToClipboard('{{ $accountNumber }}', 'Account Number')">
                                <em class="icon ni ni-copy"></em>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="Ksh {{ number_format($amountToPay) }}" readonly>
                            <button class="btn btn-outline-primary" type="button" 
                                    wire:click="copyToClipboard('{{ $amountToPay }}', 'Amount')">
                                <em class="icon ni ni-copy"></em>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Reference</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $reference }}" readonly>
                            <button class="btn btn-outline-primary" type="button" 
                                    wire:click="copyToClipboard('{{ $reference }}', 'Reference')">
                                <em class="icon ni ni-copy"></em>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-3">
                <h6><strong>Step-by-Step Instructions:</strong></h6>
                <ol class="mb-0">
                    <li>Go to M-PESA menu on your phone</li>
                    <li>Select "Lipa na M-PESA"</li>
                    <li>Select "Paybill"</li>
                    <li>Enter business number: <strong>{{ $paybillNumber }}</strong></li>
                    <li>Enter account number: <strong>{{ $accountNumber }}</strong></li>
                    <li>Enter amount: <strong>Ksh {{ number_format($amountToPay) }}</strong></li>
                    <li>Enter your M-PESA PIN and press OK</li>
                    <li>You will receive a confirmation SMS</li>
                </ol>
            </div>
            
            @if($invoice->property)
            <div class="alert alert-warning mt-3">
                <h6><strong>Invoice Details:</strong></h6>
                <p class="mb-1"><strong>Property:</strong> {{ $invoice->property->name }}</p>
                @if($invoice->house)
                <p class="mb-1"><strong>House:</strong> {{ $invoice->house->name }}</p>
                @endif
                <p class="mb-0"><strong>Balance Due:</strong> Ksh {{ number_format($invoice->balance_due, 2) }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:load', function () {
    Livewire.on('copy-to-clipboard', function (data) {
        navigator.clipboard.writeText(data.text).then(function() {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'toast toast-success';
            toast.innerHTML = data.label + ' copied to clipboard!';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 3000);
        });
    });
});
</script>

