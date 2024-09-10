<div class="nk-block">
    <div class="card card-bordered card-stretch">
        <div class="card-inner-group">
            <div class="card-inner">
                <div class="row">
                    <div class="col-sm-3">
                        <select class="form-select" wire:model="property">
                            <option value="">{{ __('Select Property')}}</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>

                        @error('property') <span class="text-danger">{{ $message }}</span> @enderror


                    </div>
                    <div class="col-sm-3 me-auto">
                        <x-form.form-monthpicker wire:model="month_year" id="mont-year-field"/>
                        @error('month_year') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-sm-3">
                        <x-button loading="{{__('Generating...')}}" wire:click="submit" class="btn btn-primary">
                            {{__('Generate Report')}}
                        </x-button>
                    </div>
                </div>

            </div><!-- .card-inner -->
            <div class="card-inner ">
                @if(count($payments)>0)
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h5 class="nk-block-title">{{ __('Property Income Breakdown.')}}</h5>
                            <div class="nk-block-des">
                                <p>{{ __('Showing income for')}} {{ $property->name }} {{ __('for the month of')}} {{ $month_year }}</p>
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->

                    <div class="nk-block-head ">
                        <div class="nk-block-head-content">
                            <h6 class="nk-block-title">{{ __('Rental Income and Deductions')}}</h6>
                        </div>
                    </div><!-- .nk-block-head -->

                    <div class="nk-block ">
                        <div class="card card-bordered card-stretch">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('Unit')}}</th>
                                    <th class="text-end" scope="col">{{ __('Total')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $payment->house->name??'' }}</td>
                                        <td class="text-end">{{ number_format($payment->amount,2) }}</td>
                                    </tr>

                                @endforeach
                                <tr class="table-secondary">
                                    <th scope="row" colspan="2">{{ __('Total Collected')}}</th>
                                    <td class="text-end">{{ number_format($payments->sum('amount'),2) }}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>



                @else
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            {{--                            <h5 class="nk-block-title text-muted px-3 text-center">No Payments Found</h5>--}}
                        </div>
                    </div>

                @endif

                @if(count($expenses)>0)
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h6 class="nk-block-title">{{ __('Expenses Breakdown')}}</h6>
                        </div>
                    </div><!-- .nk-block-head -->

                    <div class="nk-block">
                        <div class="card card-bordered card-stretch">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th colspan="2" scope="col">{{ __('Expense Description')}}</th>
                                    <th scope="col">{{ __('Unit')}}</th>
                                    <th class="text-end" scope="col">{{ __('Amount')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($expenses as $expense)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td colspan="2">{{ $expense->description }}</td>
                                        <td>{{ $expense->house->name??'' }}</td>
                                        <td class="text-end">{{ number_format($expense->amount,2) }}</td>
                                    </tr>

                                @endforeach
                                <tr class="table-secondary">
                                    <th scope="row">{{ __('Total')}}</th>
                                    <td colspan="2"></td>

                                    <td></td>
                                    <td class="text-end">{{ number_format($expenses->sum('amount'),2) }}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>



                @else
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            {{--                            <h5 class="nk-block-title text-muted px-3 text-center">No Expenses Found</h5>--}}
                        </div>
                    </div>

                @endif

                @if(count($expenses)>0 || count($payments)>0)

                    <div class="nk-block">

                        <div class="card card-bordered bg-lighter">
                            <div class="card-inner">
                                <div class="team">
                                    <ul class="team-info">
                                        <li class="fw-bolder fs-16px">
                                            <span>{{ __('Total Rent Collected')}}</span><span>{{ number_format($payments->sum('amount'),2) }}</span>
                                        </li>
                                        <li class="fw-bolder fs-16px">
                                            <span>{{ __('Total Inclusive Expenses')}}</span><span>{{ number_format($expenses->sum('amount'),2) }}</span>
                                        </li>

                                        <hr/>
                                        <li class="fw-bolder fs-17px">
                                            <span>{{ __('Net Property Income')}}</span><span>{{ number_format($payments->sum('amount') - $expenses->sum('amount'),2) }}</span>
                                        </li>
                                    </ul>

                                </div><!-- .team -->
                            </div><!-- .card-inner -->
                        </div><!-- .card -->


                    </div>


                @endif

            </div><!-- .card-inner -->

        </div><!-- .card-inner-group -->
    </div><!-- .card -->
</div><!-- .nk-block -->
