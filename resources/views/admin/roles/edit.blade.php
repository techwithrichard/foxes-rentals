@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Editing Role')}} {{ $role->name }}</h3>
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


                    @if(session()->has('success'))

                        <div class="alert alert-success alert-icon alert-dismissible mb-2">
                            <em class="icon ni ni-cross-circle"></em>
                            <strong> {{ session()->get('success') }}</strong>
                            <button class="close" data-bs-dismiss="alert"></button>
                        </div>

                    @endif

                   ROLE {{$role->id}}


                    <div class="nk-block">
                        <div class="card card-preview">
                            <div class="card-inner">

                                <form action="{{ route('admin.roles-management.update',$role->id) }}" method="post">

                                    @csrf
                                    @method('PUT')
                                    <div class="row gy-4">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label" for="default-01">
                                                    {{ __('Role Name')}}
                                                </label>
                                                <div class="form-control-wrap">
                                                    <input type="text"
                                                           id="name"
                                                           name="name"
                                                           class="form-control"
                                                           value="{{ old('name', isset($role) ? $role->name : '') }}"
                                                           required>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label" for="default-textarea">
                                                    {{ __('Associate Permissions')}}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="row gy-4">
                                                <div class="custom-control custom-checkbox d-none">
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        name="permission[]"
                                                        id="customCheckq"
                                                        value="{{ $default_permission->id }}"
                                                        checked
                                                    >
                                                    <label class="custom-control-label"
                                                           for="customCheckq">
                                                        {{ Str::upper(Str::replace('_',' ',$default_permission->name)) }}
                                                    </label>
                                                </div>
                                                @forelse($permissions as $permission)
                                                    <div class="col-md-3 col-sm-4">


                                                        <div class="custom-control custom-checkbox">
                                                            <input
                                                                type="checkbox"
                                                                class="custom-control-input"
                                                                name="permission[]"
                                                                id="customCheck{{$loop->iteration}}"
                                                                value="{{ $permission->id }}"
                                                                {{ $role->permissions->pluck('id')->contains($permission->id) ? 'checked' : '' }}
                                                            >
                                                            <label class="custom-control-label"
                                                                   for="customCheck{{$loop->iteration}}">
                                                                {{ Str::title(Str::replace('_',' ',$permission->name)) }}
                                                            </label>
                                                        </div>

                                                    </div>

                                                @empty

                                                @endforelse
                                            </div>

                                        </div>

                                        <div class="col-sm-12 text-right">
                                            <input class="btn btn-primary" type="submit" value="{{ __('Save')}}">
                                        </div>

                                    </div>
                                </form>


                            </div>
                        </div>

                    </div>
                </div>

            </div>


        </div>
    </div>



@endsection
