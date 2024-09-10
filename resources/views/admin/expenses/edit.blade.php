@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head mb-0">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Update Expense')}}</h3>
                                <div class="nk-block-des text-soft">
                                    {{ __('Update expense details carefully.')}}
                                </div>
                            </div>
                            <div class="nk-block-head-content">

                            <x-back_link href="{{ route('admin.expenses.index') }}"></x-back_link>
 
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    @livewire('admin.expenses.edit-expense-component',['expenseId' => $id])
                </div>
            </div>
        </div>
    </div>

@endsection


