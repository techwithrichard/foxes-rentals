@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ $property->name }}</h3>

                            </div>
                            <div class="nk-block-head-content">
                                <x-back_link href="{{ route('admin.properties.index') }}"></x-back_link>

                            </div>
                        </div>
                    </div><!-- .nk-block-head -->

                    @if (session()->has('success'))
                        <div class="nk-block">
                            <div class="alert alert-info alert-icon"><em class="icon ni ni-alert-circle"></em>
                                <strong>{{ session()->get('success_title') }}</strong>.
                                {{ session()->get('success') }}
                            </div>
                        </div>
                    @endif

                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered">
                            <div class="card-aside-wrap">
                                <div class="card-content">
                                    <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#tabDetails">
                                                <em class="icon ni ni-info"></em>
                                                <span>{{ __('Property Details')}}</span>
                                            </a>
                                        </li>


                                        @if ($property->is_multi_unit)
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#tabUnits">
                                                    <em class="icon ni ni-grid-alt"></em>
                                                    <span>{{ __('Property Units')}}</span>
                                                </a>
                                            </li>


                                            <li class="nav-item nav-item-trigger d-xxl-none">

                                                @can('house_create')



                                                    <a href="{{ route('admin.houses.create') }}"
                                                       class="btn btn-primary">
                                                        <em class="icon ni ni-plus"></em>
                                                        <span>{{ __('Add Unit')}}</span>
                                                    </a>
                                                @endcan


                                            </li>
                                        @endif

                                    </ul>
                                    <div class="card-inner">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tabDetails">
                                                <div class="nk-block nk-block-between">
                                                    <div class="nk-block-head">
                                                        <h6 class="title">{{__('Property Information')}}</h6>
                                                        <p>{{__('Property basic details')}}</p>
                                                    </div><!-- .nk-block-head -->
                                                    <div class="nk-block">

                                                        @can('edit property')
                                                            <a href="{{ route('admin.properties.edit',$property->id)}}"
                                                               class="btn btn-white btn-icon btn-outline-light">
                                                                <em class="icon ni ni-edit"></em>
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </div><!-- .nk-block-between  -->


                                                <div class="nk-block">

                                                    <div class="profile-ud-list">
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Property Name')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->name}}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Property Type')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->type }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('No Of Bedrooms')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->bedrooms ?? '****' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('No of Bathrooms')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->bathrooms ?? '****' }}</span>
                                                            </div>
                                                        </div>

                                                        @if(!$property->is_multi_unit)
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Defined Rent')}}</span>
                                                                    <span class="profile-ud-value">{{ setting('currency_symbol').' '. number_format($property->rent,2)
                                                                }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Defined Deposit')}}</span>
                                                                    <span class="profile-ud-value">{{ setting('currency_symbol').' '.
                                                                number_format($property->deposit,2) }}</span>
                                                                </div>
                                                            </div>

                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Occupation Status')}}</span>
                                                                    <span class="profile-ud-value">
                                                                @if($property->is_vacant)
                                                                            <span
                                                                                class="badge badge-success">{{ __('Vacant')}}</span>
                                                                        @else
                                                                            <span
                                                                                class="badge badge-danger">{{ __('Occupied')}}</span>
                                                                        @endif
                                                            </span>
                                                                </div>
                                                            </div>
                                                        @endif


                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Property Size')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->size ?? '****' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                                <span
                                                                    class="profile-ud-label">{{ __('Lease Type')}}</span>
                                                                <span class="profile-ud-value">
                                                                {{ $property->is_multi_unit? __('Leased As Units'):__('Leased As House') }}
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
                                                                    class="profile-ud-value">{{ $property->address->address_one ?? '****' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                                <span
                                                                    class="profile-ud-label">{{ __('Address Two')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->address->address_two ?? '****' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                                <span class="profile-ud-label">{{ __('City')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->address->city ?? '****' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                                <span class="profile-ud-label">{{ __('State')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->address->state ?? '****' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                                <span
                                                                    class="profile-ud-label">{{ __('Zip Code')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->address->zip ?? '****' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="profile-ud-item">
                                                            <div class="profile-ud wider">
                                                                <span class="profile-ud-label">{{ __('Country')}}</span>
                                                                <span
                                                                    class="profile-ud-value">{{ $property->address->country ?? '****' }}</span>
                                                            </div>
                                                        </div>
                                                    </div><!-- .profile-ud-list -->
                                                </div><!-- .nk-block -->


                                                @if($property->description)
                                                    <div class="nk-block">
                                                        <div class="nk-block-head nk-block-head-sm nk-block-between">
                                                            <h6 class="title overline-title text-base">
                                                                {{ __('Description')}}</h6>

                                                        </div><!-- .nk-block-head -->
                                                        <p>{{ $property->description }} </p>
                                                    </div>
                                                @endif

                                                @if($property->amenities)
                                                    <div class="nk-block">
                                                        <div class="nk-block-head nk-block-head-sm nk-block-between">
                                                            <h6 class="title overline-title text-base">
                                                                {{ __('Amenities')}}</h6>

                                                        </div><!-- .nk-block-head -->
                                                        <div>
                                                            <ul class="d-flex flex-wrap g-1">

                                                                @foreach($property->amenities as $item)
                                                                    <li><span
                                                                            class="badge badge-dim {{ badge_color() }}">{{ $item }}</span>
                                                                    </li>
                                                                @endforeach


                                                            </ul>

                                                        </div><!-- .bq-note -->
                                                    </div>
                                                @endif

                                            </div><!-- tab pane -->


                                            <div class="tab-pane" id="tabUnits">
                                                <div class="nk-block nk-block-between">
                                                    <div class="nk-block-head">
                                                        <h6 class="title">{{ __('Property Units')}}</h6>
                                                        <p>{{ __('Property has the listed property units')}}</p>
                                                    </div><!-- .nk-block-head -->
                                                    <div class="nk-block">

                                                        @can('house_create')

                                                            <a href="{{route('admin.houses.create')}}"
                                                               class="btn btn-icon btn-primary">
                                                                <em class="icon ni ni-plus"></em>
                                                            </a>

                                                        @endcan

                                                    </div>
                                                </div><!-- .nk-block-between  -->

                                                @livewire('admin.property.property-units-table-component',['propertyId'=>$property->id])
                                            </div>
                                            <!--tab pane-->

                                        </div>
                                        <!--tab content-->
                                    </div>
                                    <!--card inner-->
                                </div><!-- .card-content -->

                            </div><!-- .card-aside-wrap -->
                        </div>
                        <!--card-->
                    </div>


                </div>
            </div>
        </div>
    </div>

@endsection
