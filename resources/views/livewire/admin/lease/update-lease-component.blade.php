<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner-group">
            <div class="card-inner">
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h5 class="title nk-block-title">{{ __('Lease Details')}}</h5>
                        <p>{{ __('Important lease details')}} </p>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="row gy-4 mb-3">

                        <!--col-->
                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">
                                <label class="form-label">Lease ID
                                    <x-form.required/>
                                </label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control @error('deposit') is-invalid @enderror"
                                           id="lease_id"
                                           wire:model.defer="lease_id">
                                    @error('lease_id') <span class="invalid-feedback">{{ $message }}</span> @enderror

                                </div>

                            </div>
                        </div>

                        <!--col-->
                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('Start Date')}}</label>
                                <x-form.form-date wire:model="start_date"/>
                                @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>   <!--col-->
                        <!--col-->
                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('End Date')}}</label>
                                <x-form.form-date wire:model="end_date"/>
                                @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <!--col-->
                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('Invoice Generation Day')}}</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" wire:model="invoice_generation_day">
                                        <option value="">Select Day</option>
                                        <!--Generate options from 1 to 28-->
                                        @for($i=1; $i<=28; $i++)
                                            <option value="{{ $i }}">{{__('Day ')}} {{ $i }}</option>
                                        @endfor


                                    </select>

                                    @error('house_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!--col-->
                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('Rent Cycle')}}</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" wire:model="rent_cycle">
                                        <option value="">{{ __('Select Cycle')}}</option>
                                        <option value="1">{{ __('1 Month')}}</option>
                                        <option value="2">{{ __('2 Months')}}</option>
                                        <option value="3">{{ __('3 Months')}}</option>
                                        <option value="4">{{ __('4 Months')}}</option>
                                        <option value="6">{{ __('6 Months')}}</option>
                                        <option value="12">{{ __('12 Months')}}</option>

                                    </select>

                                    @error('house_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>


                        <!--col-->
                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="phone-no">{{ __('Monthly Rent')}}</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control" id="phone-no"
                                           wire:model.defer="rent_amount">
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="deposit">{{ __('Deposit To Pay')}}</label>
                                <div class="form-control-wrap">
                                    <input type="number" class="form-control @error('deposit') is-invalid @enderror"
                                           id="deposit"
                                           wire:model.defer="deposit">
                                    @error('deposit') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>


                    </div>
                    <!--row-->
                </div>
            </div><!-- .card-inner -->
            <div class="card-inner">

                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h5 class="title nk-block-title">{{ __('Lease Extra Bills')}}</h5>
                            <p>{{ __('Add bills that will be billed monthly,in addition to montlhy rent')}} </p>
                        </div><!-- .nk-block-head-content -->
                        <div class="nk-block-head-content">
                            @if(count($bills)==0)
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="javascript:void(0);" wire:click.prevent="addBill"
                                       class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                            class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li class="nk-block-tools-opt"><a class="btn btn-outline-light"
                                                                              href="javascript:void(0);"
                                                                              wire:click.prevent="addBill"><em
                                                        class="icon ni ni-plus"></em><span>{{ __('Add First Bill')}}</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div><!-- .toggle-wrap -->
                            @endif

                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div>

                <div class="nk-block">

                    @foreach($bills as $index=>$bill)
                        <div class="row gx-2 pb-2" id="row-{{$index}}">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <input type="text" wire:model.defer="bills.{{$index}}.name"
                                           class="form-control @error('bills.'.$index.'.name') is-invalid @enderror"
                                           id="bill-amount-{{$index}}" placeholder="Bill Name">
                                    @error('bills.'.$index.'.name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
                                <div class="form-group">

                                    <div class="form-control-wrap">
                                        <div class="d-flex gx-3">
                                            <div class="g w-100">
                                                <div class="form-control-wrap">
                                                    <input type="text"
                                                           class="form-control @error('bills.'.$index.'.amount') is-invalid @enderror"
                                                           id="bill-amount-{{$index}}"
                                                           wire:model.defer="bills.{{$index}}.amount"
                                                           placeholder="{{ __('Bill Amount')}}">

                                                    @error('bills.'.$index.'.amount')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="g d-flex d-inline-block">
                                                <button
                                                    wire:click.prevent="removeBill({{$index}})"
                                                    class="btn btn-icon btn-outline-danger me-2"><em
                                                        class="icon ni ni-minus"></em></button>

                                                @if($loop->last)
                                                    <button class="btn btn-icon btn-outline-primary"
                                                            wire:click.prevent="addBill"><em
                                                            class="icon ni ni-plus"></em></button>

                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                @endforeach



                <!--row-->
                </div>
            </div><!-- .card-inner -->

            <div class="card-inner">
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <p>
                            {{ __('To prevent confusion with the current invoice cycle, any changes to the rent cycle or invoice generation schedule will only be implemented after the next invoice generation date. However, if there are updates to the rent or bills, they will be reflected in the upcoming invoice.') }}

                        </p>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="row">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <div class="col-12 mt-4">
                            <div class="float-end">
                                <x-button wire:click="submit" loading="{{__('Updating...')}}" class="btn btn-primary">
                                    {{ __('Update Lease') }}
                                </x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .card-inner -->
        </div>
    </div><!-- .card -->
</div><!-- .nk-block -->
