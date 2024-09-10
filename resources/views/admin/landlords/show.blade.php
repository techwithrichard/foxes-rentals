@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Landlord')}}/ <strong
                                        class="text-primary small">
                                        {{ $landlord->name }}
                                    </strong>
                                </h3>

                            </div>
                            <div class="nk-block-head-content">
                                <x-back_link href="{{ route('admin.landlords.index') }}"></x-back_link>


                            </div>
                        </div>
                    </div><!-- .nk-block-head -->


                    @if (session()->has('success'))
                        <div class="nk-block">
                            <div class="alert alert-info alert-icon"><em class="icon ni ni-alert-circle"></em>
                                <strong>{{ session()->get('success') }}</strong>.

                            </div>
                        </div>
                    @endif


                    <div class="card card-bordered">


                        <ul class="nav nav-tabs nav-tabs-card">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab"
                                   href="#tabDetails">{{ __('Details')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabInvoices">{{ __('Invoices')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabVouchers">{{ __('Vouchers')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabProperties">{{ __('Properties')}}</a>
                            </li>

                        </ul>
                        <div class="tab-content card-inner">

                            <div class="tab-pane active " id="tabDetails">
                                <div class="kontenti">
                                    <div class="nk-block nk-block-between">
                                        <div class="nk-block-head">
                                            <h6 class="title">{{ __('Landlord Information')}}</h6>
                                            <p>{{ __('Landlord personal information.')}}</p>
                                        </div><!-- .nk-block-head -->
                                        <div class="nk-block">
                                            @can('edit landlord')


                                                <a class="btn btn-white btn-icon btn-outline-light"
                                                   href="{{ route('admin.landlords.edit',$landlord->id) }}">
                                                    <em class="icon ni ni-edit"></em>
                                                </a>
                                            @endcan
                                        </div>
                                    </div><!-- .nk-block-between  -->
                                    <div class="nk-block">
                                        <div class="profile-ud-list">
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Name')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->name }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Email')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->email }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Phone Number')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->phone }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Address')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->address }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Identity Number')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->identity_no }}</span>
                                                </div>
                                            </div>

                                            @if($landlord->identity_document)
                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span
                                                            class="profile-ud-label">{{ __('Identity Document')}}</span>
                                                        <span class="profile-ud-value">
                                                            <a href="{{ url($landlord->identity_document) }}" download
                                                               target="_blank">
                                                                <em class="icon ni ni-download-cloud"></em>
                                                                {{ __('view')}}
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif


                                        </div><!-- .profile-ud-list -->
                                    </div><!-- .nk-block -->
                                    <div class="nk-divider divider md"></div>
                                    <div class="nk-block">
                                        <div class="nk-block-head nk-block-head-line">
                                            <h6 class="title overline-title text-base">
                                                {{ __('Particulars Ownership')}}
                                            </h6>
                                        </div><!-- .nk-block-head -->
                                        <div class="profile-ud-list">
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Total Properties')}}</span>
                                                    <span class="profile-ud-value">
                                                        <span class="badge badge-pill bg-warning">
                                                            {{ $landlord->properties->count() }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Total Units')}}</span>
                                                    <span class="profile-ud-value">
                                                        <span class="badge badge-pill bg-success">
                                                            {{ $landlord->houses->count() }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>

                                        </div><!-- .profile-ud-list -->
                                    </div><!-- .nk-block -->
                                    <div class="nk-divider divider md"></div>
                                    <div class="nk-block">
                                        <div class="nk-block-head nk-block-head-line">
                                            <h6 class="title overline-title text-base">
                                                {{ __('Next Of Kin Details')}}
                                            </h6>
                                        </div><!-- .nk-block-head -->
                                        <div class="profile-ud-list">
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Name')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->kin_name??__('N/a') }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Identity Number')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->kin_identity??__('N/a') }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Phone Number')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->kin_phone??__('N/a') }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Relationship')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $landlord->kin_relationship??__('N/a') }}</span>
                                                </div>
                                            </div>
                                        </div><!-- .profile-ud-list -->
                                    </div><!-- .nk-block -->

                                </div>
                            </div>
                            <div class="tab-pane" id="tabInvoices">
                                @can('view custom invoice')
                                    <div class="nk-block nk-block-lg">


                                        <div class="card">
                                            <div class="">
                                                <table class="datatable-init nowrap table">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ __('Inv No')}}:</th>
                                                        <th>{{ __('Inv Date')}}</th>
                                                        <th>{{ __('Due Date')}}</th>
                                                        <th>{{ __('Amount')}}</th>
                                                        <th>{{ __('Property')}}</th>
                                                        <th class="text-end">{{ __('Actions')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @foreach($invoices as $invoice)
                                                        <tr>
                                                            <td>
                                                                {{ $invoice->invoice_id }}
                                                                {{--                                                            <a href="{{ route('admin.invoices.show',$invoice->id) }}">--}}
                                                                {{--                                                                {{ $invoice->invoice_number }}--}}
                                                                {{--                                                            </a>--}}
                                                            </td>
                                                            <td>{{ $invoice->invoice_date->format('d M, Y') }}</td>
                                                            <td>{{ $invoice->due_date?->format('d M, Y') }}</td>
                                                            <td>{{ setting('currency_symbol').' '. number_format($invoice->items_sum_cost,2) }}</td>
                                                            <td>{{ $invoice->property->name??'N/a' }}</td>
                                                            <td class="text-end">
                                                                <a href="{{ route('admin.custom-invoice.show',$invoice->id) }}"
                                                                   class="btn btn-sm btn-primary">
                                                                    <em class="icon ni ni   ni-eye"></em>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach


                                                    </tbody>
                                                </table>
                                            </div>
                                        </div><!-- .card-preview -->
                                    </div> <!-- nk-block -->
                                @endcan

                            </div>
                            <div class="tab-pane" id="tabVouchers">
                              @can('view landlord voucher')
                                    <div class="nk-block nk-block-lg">
                                        <div class="card">
                                            <div class="">
                                                <table class="datatable-init nowrap table">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('Type')}}</th>
                                                        <th>{{ __('Date')}}</th>
                                                        <th>{{ __('Amount')}}</th>
                                                        <th>{{ __('Property')}}</th>
                                                        <th>{{ __('Unit')}}</th>
                                                        <th class="text-end">{{ __('View')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @foreach($vouchers as $voucher)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $voucher->type }}</td>
                                                            <td>{{ $voucher->voucher_date->format('d M,Y') }}</td>
                                                            <td>{{ setting('currency_symbol') }} {{ number_format($voucher->items_sum_cost,2) }}</td>
                                                            <td>{{ $voucher->property->name??'N/a' }}</td>
                                                            <td>{{ $voucher->unit->name??'N/a' }}</td>
                                                            <td class="text-end">
                                                                <a href="{{ route('admin.vouchers.show',$voucher->id) }}"
                                                                   class="btn btn-sm btn-primary">
                                                                    <em class="icon ni ni   ni-eye"></em>
                                                                </a>
                                                            </td>

                                                        </tr>

                                                    @endforeach


                                                    </tbody>
                                                </table>
                                            </div>
                                        </div><!-- .card-preview -->
                                    </div> <!-- nk-block -->
                              @endcan
                            </div>
                            <div class="tab-pane" id="tabProperties">
                               @can('view property')
                                    <div class="nk-block nk-block-lg">
                                        <div class="card">
                                            <div class="">
                                                <table class="datatable-init nowrap table">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('Name')}}</th>
                                                        <th>{{ __('Type')}}</th>
                                                        <th>{{ __('Status')}}</th>
                                                        <th class="text-end">{{ __('Details')}}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @foreach($landlord->properties as $property)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $property->name }}</td>
                                                            <td>{{ $property->type }}</td>
                                                            <td>
                                                                @include('admin.property.partials.status')
                                                            </td>
                                                            <td class="text-end">
                                                                <a href="{{ route('admin.properties.show',$property->id) }}"
                                                                   class="btn btn-sm btn-primary">
                                                                    <em class="icon ni ni   ni-eye"></em>
                                                                </a>
                                                            </td>
                                                        </tr>

                                                    @endforeach


                                                    </tbody>
                                                </table>
                                            </div>
                                        </div><!-- .card-preview -->
                                    </div> <!-- nk-block -->
                               @endcan
                            </div>

                        </div>


                    </div><!-- .components-preview -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            //listen to when #tabLeases is selected
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
                // alert('tab changed');
            });
        });
    </script>

@endpush
