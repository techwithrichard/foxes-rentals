<div class="row mb-4 g-0">
    <div class="col-md-2">
        <x-form.form-date wire:model.defer="from_date" id="from-date-filter" placeholder="From Date"/>
    </div>
    <div class="col-md-2">
        <x-form.form-date wire:model.defer="to_date" id="to-date-filter" placeholder="To Date"/>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="status-filter" wire:model.defer="paymentStatus">
            <option value="">{{ __('Status')}}</option>
            <option value="paid">{{ __('Approved')}}</option>
            <option value="pending">{{ __('Pending')}}</option>
            <option value="cancelled">{{ __('Rejected')}}</option>

        </select>

    </div>

    <div class="col-md-1">
        <x-button class="btn btn-primary" wire:click="exportPayments">{{ __('Export')}}</x-button>
    </div>


    <div class="col-md-3 ms-auto">
        <input type="text" class="form-control" id="searchInput" placeholder="Search">
    </div>
</div>
