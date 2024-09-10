@extends('layouts.main')
@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Users Lists')}}</h3>

                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                       data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li>
                                                <a href="{{ route('admin.users-management.create') }}"
                                                   class="btn btn-primary">
                                                    <em class="icon ni ni-plus"></em>
                                                    <span>{{ __('Create User')}}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div><!-- .toggle-wrap -->
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->

                    @if(session()->has('success'))
                        <div class="alert alert-success alert-icon alert-dismissible">
                            <em class="icon ni ni-check-circle"></em>
                            <strong>{{ session('success') }}</strong>
                            <button class="close" data-bs-dismiss="alert"></button>
                        </div>

                    @endif


                    <div class="nk-block">
                        <div class="card card-bordered card-stretch">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('User')}}</th>
                                    <th scope="col">{{ __('Email')}}</th>
                                    <th scope="col">{{ __('Phone')}}</th>
                                    <th scope="col">{{ __('Roles')}}</th>
                                    <th class="text-end" scope="col">{{ __('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>

                                            <ul class="preview-list">
                                                @forelse($user->roles as $role)

                                                    <li class="preview-item">
                                                        <span
                                                            class="badge rounded-pill bg-primary">  {{ Str::upper($role->name )}}</span>
                                                    </li>
                                                @empty

                                                @endforelse


                                            </ul>
                                        </td>

                                        <td>
                                            <ul class="nk-tb-actions gx-0">

                                                {{--                                                @if(auth()->id()!=$user->id)--}}

                                                <li>

                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                           data-bs-toggle="dropdown"><em
                                                                class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <ul class="link-list-opt no-bdr">

                                                                <li>
                                                                    <form
                                                                        action="{{ route('admin.users-management.destroy',$user->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('delete this user ?');">

                                                                        @csrf
                                                                        @method('DELETE')

                                                                        <button type="submit" class="btn btn-link">
                                                                            <em class="icon ni ni-trash-fill"></em>
                                                                            <span>{{ __('Remove User')}}</span>
                                                                        </button>
                                                                    </form>

                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>

                                                {{--                                                @endif--}}

                                            </ul>

                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>

                        </div><!-- .card -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>

@endsection
