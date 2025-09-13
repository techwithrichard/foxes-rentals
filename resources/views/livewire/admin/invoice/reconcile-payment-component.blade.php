<div class="card-inner">
    <div class="nk-block">

        <div class="nk-block">

            <!--Form with select class-->

            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Select Tenant</label>
                <x-virtual-select id="selectedTenant"
                                  name="selectedTenant"
                                  wire:model="selectedTenant"
                                  options="tenants"/>
            </div>

        </div>


    </div>

    <div class="nk-block">
        <h6 class="lead-text mb-0">Tenant Invoices</h6>
        <p class="text-soft mb-3">
            Invoices for the selected tenant.Select the invoice to reconcile payment, or click ignore to ignore the
            invoice.
        </p>
        <div class="row g-3">


            @forelse($invoices as $invoice)

                <div class="col-12 mt-2">
                    <ul class="custom-control-group custom-control-vertical custom-control-stacked w-100">
                        <li>
                            <div class="custom-control custom-control-sm custom-radio custom-control-pro">
                                <input type="radio" class="custom-control-input"
                                       value="{{$invoice->id}}"
                                       wire:model.defer="selectedInvoice"
                                       id="invoice-select-s{{ $loop->iteration }}" name="user-choose">
                                <label class="custom-control-label" for="invoice-select-s{{ $loop->iteration }}">
                                <span class="user-card">

                                    <span class="user-name">Invoice: {{ $invoice->invoice_id }}</span>
                                    <span class="user-name ps-4">
                                        Balance Due: {{ $invoice?->balance_due }}
                                    </span>
                                    <span class="user-name ps-4">
                                        Property: {{$invoice->property?->name}}
                                    </span>
                                    @if($invoice->house)
                                        <span class="user-name ps-4">House: {{$invoice->house?->name}}</span>
                                    @endif
                                    <span class="user-name ps-4">
                                        Invoice Date: {{$invoice->created_at?->format('d M, Y')}}
                                    </span>
                                </span>
                                </label>
                            </div>
                        </li>

                    </ul>
                </div>
            @empty

                @if($selectedTenant)
                    <div class="col-12">
                        <div class="alert alert-warning">
                            No invoices found for the selected tenant.
                        </div>
                    </div>

                @else
                    <div class="col-12">
                        <div class="alert alert-warning">
                            Select a tenant to view invoices.
                        </div>
                    </div>
                @endif

            @endforelse


            @if($errors->any())
                <hr>
                <div class="col-12">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                </div>

            @endif

            @if(session('error'))
                <div class="col-12">
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <div class="d-flex justify-content-between mt-5">
                <x-button wire:click="ignoreReconciliation" loading="{{__('submitting...')}}"
                          class="btn btn-outline-primary">
                    Ignore Reconciliation
                </x-button>

                <x-button wire:click="commitReconciliation" loading="{{__('submitting...')}}" class="btn btn-success">
                    Commit Reconciliation
                </x-button>
            </div>

        </div><!-- .row -->
    </div>
</div><!-- .card-inner -->
