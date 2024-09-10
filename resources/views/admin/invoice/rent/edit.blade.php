@extends('layouts.main')

@section('content')
    <livewire:admin.invoice.modify-invoice-component :invoice_id="$invoice_id"/>

@endsection
