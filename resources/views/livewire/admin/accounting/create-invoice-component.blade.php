<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="card-head mb-3">
                <h5 class="card-title">{{ __('Add Invoice Details')}}</h5>
            </div>

            <hr class="divider"/>

            <div class="row gx-1 mb-2">
                <div class="col-sm-4 me-auto">
                    <div class="form-group mb-0">
                        <label class="form-label" for="default-06">{{ __('Particulars')}}</label>
                        <div class="form-control-wrap ">
                            <div class="form-control-select">
                                <select class="form-control @error('landlord') is-invalid @enderror" id="default-06"
                                        wire:model="landlord">
                                    <option value="">{{ __('Select Landlord')}}</option>
                                    @foreach($landlords as $landlord)
                                        <option value="{{ $landlord->id }}">{{ $landlord->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-1 mb-1">
                        <div class="form-control-wrap ">
                            <div class="form-control-select">
                                <select class="form-control @error('property') is-invalid @enderror" id="default-06"
                                        wire:model="property">
                                    <option value="">{{ __('Select Property')}}</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-0">
                        <div class="form-control-wrap ">
                            <div class="form-control-select">
                                <select class="form-control @error('unit') is-invalid @enderror" id="default-07"
                                        wire:model="unit">
                                    <option value="">{{ __('Select Unit')}}</option>
                                    @foreach($units as $uniti)
                                        <option value="{{ $uniti->id }}">{{ $uniti->name }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">

                    <div class="mb-0 row">
                        <label for="staticEmail" class="col-sm-4 col-form-label">{{ __('Invoice Date')}}</label>
                        <div class="col-sm-8">
                            <x-form.form-date wire:model="invoice_date"/>
                        </div>
                    </div>
                    <div class="mb-0 row">
                        <label for="staticEmail" class="col-sm-4 col-form-label">{{ __('Due Date')}}</label>
                        <div class="col-sm-8">
                            <x-form.form-date wire:model="dueDate"/>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="divider"/>

            <div class="border rounded-2 border-light">
                <table class="table table-borderless">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th class="w-60" scope="col">{{ __('Item Description')}}</th>
                        <th class="text-center" scope="col">{{ __('Quantity')}}</th>
                        <th class="text-center" scope="col">{{ __('Total')}}</th>
                        <th class="w-5"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $index=>$item)
                        <tr>
                            <th class="mx-auto" scope="row">{{ $loop->iteration }}</th>
                            <td>
                                <input type="text" class="form-control " id="item-description{{ $index }}"
                                       wire:model.defer="items.{{ $index }}.description"
                                       placeholder="Item Description">


                            </td>
                            <td>
                                <input type="number" class="form-control text-center" id="item-quantity{{ $index }}"
                                       wire:model.defer="items.{{ $index }}.quantity"
                                       placeholder="Quantity">
                            </td>
                            <td>
                                <input type="number"
                                       class="form-control text-center @error('items.'.$index.'.amount') is-invalid @enderror"
                                       id="item-total{{ $index }}"
                                       wire:model="items.{{ $index }}.amount" min="0" step="any"
                                       placeholder="Total">

                                @error('items.{{ $index }}.amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </td>
                            <td class="tb-col-action">
                                @if($index>0)
                                    <a href="#" class="link-cross me-sm-n1"
                                       wire:click.prevent="removeItem({{ $index }})">
                                        <em class="icon ni ni-cross"></em>
                                    </a>
                                @endif


                            </td>
                        </tr>

                    @endforeach


                    <tr class="border-bottom">
                        <th colspan="4">
                            <a href="javascript:void(0);" wire:click.prevent="addItem">{{ __('Add New Row')}}</a>
                        </th>
                    </tr>


                    <tr>
                        <th colspan="2">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <textarea class="form-control" rows="1" wire:model.defer="invoiceNotes"
                                              placeholder="Invoice notes"
                                              id="default-textarea"></textarea>
                                </div>
                            </div>
                        </th>
                    </tr>

                    <tr>

                        <th colspan="5">

                            <div class="float-end">

                                <div class="mb-0 row">
                                    <label for="staticEmail"
                                           class="col-sm-5 col-form-label">{{ __('Total')}}</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control text-center" id="total-amount" readonly
                                               value="{{ number_format($totalAmount,2) }}"
                                               placeholder="Total">
                                    </div>
                                </div>
                            </div>

                        </th>

                    </tr>
                    </tbody>
                </table>
            </div>

            @if($errors->any())
                <div class="alert alert-danger mt-2 mb-2">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger mt-2 mb-2">
                    {{ session('error') }}
                </div>

            @endif

            <div class="row mt-3">
                <div class="col-sm-12">

                    <button type="button" class="btn btn-lg btn-primary float-end" wire:click="saveInvoice"
                            wire:loading.class="disabled"
                            id="{{ uniqid()}}"
                            data-redirect="no" wire:loading.attr="disabled">
                        <div wire:loading wire:target="saveInvoice">
                            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true"
                                  wire:loading
                                  wire:target="saveInvoice">

                            </span>
                        </div>
                        <span>{{ __('Save Invoice')}}</span>
                    </button>
                </div>
            </div>


        </div>
    </div>
</div><!-- .nk-block -->
