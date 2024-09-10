@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ $role->name }}</h3>
                                <div class="nk-block-des text-soft">
                                    {{$role->name}} {{ __('has a total of')}} {{ $role->permissions->count() }} {{ __('associated
                                    permissions.')}}


                                </div>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1"
                                       data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">

                                            <li class="nk-block-tools">
                                                <a href="{{ route('admin.roles-management.index') }}"


                                                   class="btn btn-primary "><em
                                                        class="icon ni ni-arrow-to-left"></em><span>{{ __('Roles Listings')}}</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div><!-- .toggle-wrap -->
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->


                    <div class="nk-block">
                        <div class="card card-preview">
                            <div class="card-inner">


                                <div class="row gy-4">

                                    <div class="col-sm-12">
                                        <h5>{{ __('Associated Permissions')}}</h5>

                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row gy-4">

                                            @forelse($role->permissions as $permission)
                                                <div class="col-md-3 col-sm-4">
                                                    <span>
                                                        <em class="icon ni ni-chevrons-right"></em>
                                                        {{ Str::title(Str::replace('_',' ',$permission->name)) }}
                                                    </span>



                                                </div>

                                            @empty

                                            @endforelse
                                        </div>

                                    </div>


                                </div>


                            </div>
                        </div>

                    </div>
                </div>

            </div>


        </div>
    </div>



@endsection
