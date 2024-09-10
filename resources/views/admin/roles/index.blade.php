@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Roles And Permissions')}}</h3>
                                
                                <div class="nk-block-des text-soft">


                                </div>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1"
                                       data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">

                                            <li class="nk-block-tools">
                                                <a href="{{ route('admin.roles-management.create') }}"
                                                   class="btn btn-primary "><em
                                                        class="icon ni ni-plus"></em><span>{{ __('Create New Role')}}</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div><!-- .toggle-wrap -->
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->


                    @if(session()->has('success'))

                        <div class="alert alert-success alert-icon alert-dismissible mb-2">
                            <em class="icon ni ni-check-circle"></em>
                            <strong> {{ session()->get('success') }}</strong>
                            <button class="close" data-bs-dismiss="alert"></button>
                        </div>

                    @endif


                    <div class="nk-block">
                        <div class="card card-preview">
                            <div class="card-inner">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ __('Role Name')}}</th>
                                        <th scope="col">{{ __('Associated Permissions')}}</th>
                                        <th scope="col" class="text-right">{{ __('Actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @forelse($roles as $role)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ Str::ucfirst($role->name) }}</td>
                                            <td>
                                                <a href="{{ route('admin.roles-management.show',$role) }}">{{ __('View Permissions')}}
                                                    <span
                                                        class="badge bg-primary ml-2">{{number_format( $role->permissions_count )}}</span></a>
                                            </td>
                                            <td>
                                                <ul class="nk-tb-actions gx-1">
                                                    <li class="">
                                                        <a href="{{ route('admin.roles-management.edit',$role->id) }}"
                                                           class="btn btn-icon btn-trigger btn-tooltip"
                                                           title=""
                                                           data-original-title="Edit Role Permissions">
                                                            <em class="icon ni ni-edit"></em>
                                                        </a>
                                                    </li>
                                                    <li class=""><a href="#"
                                                                    class="btn btn-icon btn-trigger btn-tooltip"
                                                                    title="" data-toggle="dropdown"
                                                                    data-original-title="{{ __('Delete Role')}}">
                                                            <em class="icon ni ni-trash-fill"></em>
                                                        </a>
                                                    </li>
                                                    <li></li>

                                                </ul>
                                            </td>
                                        </tr>

                                    @empty

                                    @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


        </div>
    </div>



@endsection
