@extends('layouts.main')
@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ $house->name }}</h3>

                            </div>
                            <div class="nk-block-head-content">

                            <x-back_link href="{{ route('admin.houses.index') }}"></x-back_link>
                                  

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


                    <div class="card card-bordered components-preview wide-md mx-auto">


                        <ul class="nav nav-tabs nav-tabs-card">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tabDetails">{{ __('House Details')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabLeases">{{ __('Leasing History')}}</a>
                            </li>

                        </ul>
                        <div class="tab-content card-inner">

                            <div class="tab-pane active " id="tabDetails">
                                <div class="kontenti">
                                    <div class="nk-block nk-block-between">
                                        <div class="nk-block-head">
                                            <h6 class="title">{{__('House Information')}}</h6>
                                            <p>{{__('House basic details')}}</p>
                                        </div><!-- .nk-block-head -->
                                        <div class="nk-block">

                                            {{--                                                        @can('property_update')--}}
                                            <a href="{{ route('admin.houses.edit',$house->id)}}"
                                               class="btn btn-white btn-icon btn-outline-light">
                                                <em class="icon ni ni-edit"></em>
                                            </a>
                                            {{--                                                        @endcan--}}
                                        </div>
                                    </div><!-- .nk-block-between  -->


                                    <div class="nk-block">

                                        <div class="profile-ud-list">
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Name')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->name}}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('House Type')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->type }}</span>
                                                </div>
                                            </div>


                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Defined Rent')}}</span>
                                                    <span class="profile-ud-value">{{ setting('currency_symbol').' '. number_format($house->rent,2)
                                                                }}</span>
                                                </div>
                                            </div>

                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Landlord ')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->landlord->name ??''}}</span>
                                                </div>
                                            </div>

                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Landlord Commission')}}</span>
                                                    <span class="profile-ud-value">{{ number_format($house->commission,2)}} %</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Electricity ID')}}</span>
                                                    <span class="profile-ud-value">{{ $house->electricity_id }}</span>
                                                </div>
                                            </div>

                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Status')}}</span>
                                                    <span class="profile-ud-value">
                                                               @switch($house->status)
                                                            @case(\App\Enums\HouseStatusEnum::VACANT->value)
                                                            <span class="badge rounded-pill bg-danger">{{ __('Vacant')}}</span>
                                                            @break
                                                            @case(\App\Enums\HouseStatusEnum::OCCUPIED->value)
                                                            <span class="badge rounded-pill bg-success">{{ __('Rented')}}</span>
                                                            @break
                                                            @case(\App\Enums\HouseStatusEnum::UNDER_MAINTENANCE->value)
                                                            <span class="badge rounded-pill bg-info">{{ __('Under Maintenance')}}</span>
                                                            @break
                                                            @default
                                                            <span class="badge rounded-pill bg-info">{{ __('Unknown')}}</span>
                                                        @endswitch

                                                  </span>
                                                </div>
                                            </div>


                                        </div><!-- .profile-ud-list -->
                                    </div><!-- .nk-block -->
                                    <div class="nk-block">
                                        <div class="nk-block-head nk-block-head-line">
                                            <h6 class="title overline-title text-base">
                                                {{ __('Address Details')}}</h6>
                                        </div><!-- .nk-block-head -->
                                        <div class="profile-ud-list">
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                                <span
                                                                    class="profile-ud-label">{{ __('Address One')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->property->address->address_one ?? '****' }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                                <span
                                                                    class="profile-ud-label">{{ __('Address Two')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->property->address->address_two ?? '****' }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('City')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->property->address->city ?? '****' }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('State')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->property->address->state ?? '****' }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                                <span
                                                                    class="profile-ud-label">{{ __('Zip Code')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->property->address->zip ?? '****' }}</span>
                                                </div>
                                            </div>
                                            <div class="profile-ud-item">
                                                <div class="profile-ud wider">
                                                    <span class="profile-ud-label">{{ __('Country')}}</span>
                                                    <span
                                                        class="profile-ud-value">{{ $house->property->address->country ?? '****' }}</span>
                                                </div>
                                            </div>
                                        </div><!-- .profile-ud-list -->
                                    </div><!-- .nk-block -->


                                    @if($house->description)
                                        <div class="nk-block">
                                            <div class="nk-block-head nk-block-head-sm nk-block-between">
                                                <h6 class="title overline-title text-base">
                                                    {{ __('Description')}}</h6>

                                            </div><!-- .nk-block-head -->
                                            <p>{{ $house->description }} </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="tab-pane" id="tabLeases">

                                <div class="nk-block nk-block-lg">


                                    <div class="">
                                        <div class="">
                                            <table class="datatable-init nowrap table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Tenant Name')}}</th>
                                                    <th>{{ __('Start Date')}}</th>
                                                    <th>{{ __('End Date')}}</th>
                                                    <th>{{ __('Rent')}}</th>
                                                    <th>{{ __('Bills')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($house->leases as $lease)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $lease->tenant->name }}</td>
                                                        <td>{{ $lease->start_date->format('d M,Y') }}</td>
                                                        <td>
                                                            {{ $lease->deleted_at?$lease->start_date->format('d M,Y'):'Active' }}
                                                        </td>
                                                        <td>{{ setting('currency_symbol') }} {{ number_format($lease->rent,2) }}</td>
                                                        <td>{{ setting('currency_symbol') }} {{ number_format($lease->bills->sum('amount'),2) }}</td>
                                                    </tr>

                                                @endforeach


                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- .card-preview -->
                                </div> <!-- nk-block -->

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
