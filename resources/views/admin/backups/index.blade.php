@extends('layouts.main')
@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Data BackUps')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>{{ __('Clear your old backups regularly.')}}</p>
                                </div>

                            </div>

                            @can('create backup')
                                <div class="nk-block-head-content">
                                    <a href="{{ route('admin.backups.create')}}"
                                       class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em
                                            class="icon ni ni-ticket-plus"></em><span>{{ __('Create Back Up')}}</span></a>
                                    <a href="{{ route('admin.backups.create')}}"
                                       class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none"><em
                                            class="icon ni ni-ticket-plus"></em></a>
                                </div>
                            @endcan
                        </div>
                    </div><!-- .nk-block-head -->


                    @if (session()->has('success'))
                        <div class="nk-block">
                            <div class="alert alert-info alert-icon"><em class="icon ni ni-alert-circle"></em>
                                <strong>{{ session()->get('success') }}</strong>.

                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="nk-block">
                            <div class="alert alert-danger alert-icon"><em class="icon ni ni-cross-circle-fill"></em>
                                <strong>{{ session()->get('error') }}</strong>.

                            </div>
                        </div>
                    @endif

                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="col-12">
                                <div class="card card-bordered card-preview">
                                    <table class="table table-ulogs">
                                        <thead>
                                        <tr>
                                            <th class="w-6">#</th>
                                            <th class="tb-col-os"><span
                                                    class="overline-title">{{ __('File Name')}}</span></th>
                                            <th class="tb-col-action"><span class="overline-title">&nbsp;</span></th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($backups as $backup)
                                            <tr>
                                                <td class="w-6">
                                                    <span class="tb-lead">{{ $loop->iteration }}</span>
                                                </td>
                                                <td class="tb-col-os">
                                                    <a href="{{ route('admin.backups.show',$backup) }}" download>
                                                        <span class="tb-sub">{{ $backup }}</span>
                                                    </a>


                                                </td>
                                                <td class="tb-col-action">

                                                    @can('delete backup')



                                                        <form action="{{ route('admin.backups.destroy',$backup) }}"
                                                              method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                Delete
                                                            </button>
                                                        </form>

                                                    @endcan

                                                </td>

                                            </tr>
                                        @endforeach


                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

