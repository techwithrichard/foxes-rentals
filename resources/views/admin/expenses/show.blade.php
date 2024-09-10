@extends('layouts.main')
@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Expense Details')}}</h3>

                            </div>
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.expenses.index') }}"></x-back_link>
                                  
                             
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->

                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="kontenti">


                                <div class="nk-block">

                                    <div class="profile-ud-list">
                                        <div class="profile-ud-item">
                                            <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Name')}}</span>
                                                <span
                                                    class="profile-ud-value">{{ $expense->description}}</span>
                                            </div>
                                        </div>
                                        <div class="profile-ud-item">
                                            <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Expense Type')}}</span>
                                                <span
                                                    class="profile-ud-value">
                                                    <span class="badge badge-dim bg-outline-primary">
                                                        {{ $expense->category->name }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>


                                        <div class="profile-ud-item">
                                            <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Amount')}}</span>
                                                <span class="profile-ud-value">{{ setting('currency_symbol').' '. number_format($expense->amount,2)
                                                                }}</span>
                                            </div>
                                        </div>

                                        <div class="profile-ud-item">
                                            <div class="profile-ud wider">
                                                <span class="profile-ud-label">{{ __('Landlord')}}</span>
                                                <span
                                                    class="profile-ud-value">{{ $expense->landlord->name ??'N/a'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-ud-item">
                                            <div class="profile-ud wider">
                                                <span class="profile-ud-label">{{ __('Property')}}</span>
                                                <span
                                                    class="profile-ud-value">{{ $expense->property->name ??'N/a'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-ud-item">
                                            <div class="profile-ud wider">
                                                <span class="profile-ud-label">{{ __('Unit')}}</span>
                                                <span
                                                    class="profile-ud-value">{{ $expense->house->name ??'N/a'}}</span>
                                            </div>
                                        </div>

                                        <div class="profile-ud-item">
                                            <div class="profile-ud wider">
                                                <span class="profile-ud-label">{{ __('Expense Receipt')}}</span>
                                                <span
                                                    class="profile-ud-value">
                                                    @if($expense->receipt)
                                                        <a href="{{ url($expense->receipt) }}" download target="_blank">
                                                            {{ __('View Receipt') }}
                                                        </a>

                                                    @endif
                                                </span>
                                            </div>
                                        </div>


                                    </div><!-- .profile-ud-list -->
                                </div><!-- .nk-block -->

                                @if($expense->notes)
                                    <div class="nk-block">
                                        <div class="nk-block-head nk-block-head-sm nk-block-between">
                                            <h6 class="title overline-title text-base">
                                                {{ __('Extra Notes')}}</h6>

                                        </div><!-- .nk-block-head -->
                                        <p>{{ $expense->notes}} </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


