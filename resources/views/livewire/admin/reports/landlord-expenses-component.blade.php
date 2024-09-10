<div class="nk-block">
    <div class="card card-bordered card-stretch">
        <div class="card-inner-group">
            <div class="card-inner">

                <div class="row g-0 mb-2">
                    <div class="col-md-2">
                        <x-form.form-date wire:model="from_date" id="from-date" placeholder="From Date"/>
                    </div>
                    <div class="col-md-2">
                        <x-form.form-date wire:model="to_date" id="to-date" placeholder="To Date"/>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="landlord" wire:model="landlord_id">
                            <option value="">Select Landlord</option>
                            @foreach($landlords as $landlord)
                                <option value="{{$landlord->id}}">{{$landlord->name}}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-3 ms-auto float-end">
                        <x-button loading="{{__('Generating...')}}" wire:click="fetchReport" class="btn btn-primary">
                            {{__('Generate Report')}}
                        </x-button>

                    </div>
                </div>


            </div>

            <div class="card-inner">
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h5 class="nk-block-title">{{ __('Landlord Expenses')}}</h5>
                        <div class="nk-block-des mb-4">
                            <p>Landlord <strong>{{ $landlord_name }}</strong>
                             {{ __('expenses report for expenses incurred between')}}
                                <strong>{{$from_date}}</strong> {{ __('and')}}
                                <strong>{{$to_date}}</strong></p>
                        </div>
                    </div>

                    @forelse($expenses->groupBy('category.name') as $categoryName=>$categoryExpenses)

                        <table class="table table-bordered pb-0">
                            <thead>
                            <tr>
                                <th colspan="3" class="bg-light">{{$categoryName}}</th>
                            </tr>
                            <tr>
                                <th class="w-75">{{ __('Expense')}}</th>
                                <th>{{ __('Date Incurred')}}</th>
                                <th>{{ __('Amount')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categoryExpenses as $expense)
                                <tr>
                                    <td class="w-75">{{$expense->description}}</td>
                                    <td>{{$expense->incurred_on?->format('M d, Y')}}</td>
                                    <td>{{$expense->amount}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2" class="text-right font-weight-bold"> {{ __('Sub Total')}}
                                </td>

                                <td class="font-weight-bold">{{$categoryExpenses->sum('amount')}}</td>
                            </tr>


                            </tbody>
                        </table>
                    @empty
                        <div class="alert alert-warning mt-3 mb-3">
                            {{ __('No expenses found for the selected period and landlord.')}}
                        </div>

                    @endforelse
                    @if(count($expenses) > 0)




                        <table class="table table-bordered">
                            <tbody>


                            <tr>
                                <td colspan="3" class="fw-bolder text-end">{{ __('Total')}}

                                    <span class="text-end">{{ number_format($expenses->sum('amount'),2) }}</span>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                    @endif


                </div><!-- .nk-block-head -->
            </div>

            @if(count($expenses) > 0)
                <div class="card-inner">
                    <x-button loading="{{__('Printing...')}}" wire:click="printReport" class="btn btn-secondary">
                        <em class="icon ni ni-printer-fill"></em>
                        {{__('Generate Report')}}
                    </x-button>
                </div>

            @endif


        </div><!-- .card-inner-group -->
    </div><!-- .card -->
</div><!-- .nk-block -->
