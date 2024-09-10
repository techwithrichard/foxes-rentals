@if($proof->invoice->invoicable_type == \App\Enums\InvoicableTypeEnum::RENT)
    <span class="tb-lead"><a href="{{ route('admin.rent-invoice.show',$proof->invoice_id) }}">#{{$proof->invoice_id}}</a></span>
@else
    <span class="tb-lead"><a href="{{ route('admin.bills-invoice.show',$proof->invoice_id) }}">#{{$proof->invoice_id}}</a></span>
@endif

