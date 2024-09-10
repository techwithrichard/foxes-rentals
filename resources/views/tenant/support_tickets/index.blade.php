@extends('layouts.tenant_layout')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Raise An Issue / Create Ticket')}}</h3>
                         
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1"
                                       data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li><a href="{{ route('tenant.support-tickets.create')}}"
                                                   class="btn btn-primary"><em
                                                        class="icon ni ni-plus"></em><span>
                                                    {{ __('Create Ticket')}}
                                                </span></a></li>

                                        </ul>
                                    </div>
                                </div><!-- .toggle-wrap -->
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->

                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>

                    @endif

                    <div class="nk-block">
                        <div class="card card-bordered">
                            <table class="table table-tickets">
                                <thead class="tb-ticket-head">
                                <tr class="tb-ticket-title">
                                    <th class="tb-ticket-id"><span>{{ __('Ticket')}}</span></th>
                                    <th class="tb-ticket-desc">
                                        <span>{{ __('Subject')}}</span>
                                    </th>
                                    <th class="tb-ticket-date tb-col-md">
                                        <span>{{ __('Submited')}}</span>
                                    </th>
                                    <th class="tb-ticket-seen tb-col-md">
                                        <span>{{ __('Last Seen')}}</span>
                                    </th>
                                    <th class="tb-ticket-status">
                                        <span>{{ __('Status')}}</span>
                                    </th>
                                    <th class="tb-ticket-action"> &nbsp;</th>
                                </tr><!-- .tb-ticket-title -->
                                </thead>
                                <tbody class="tb-ticket-body">
                                @foreach($tickets as $ticket)
                                    <tr class="tb-ticket-item">
                                        <td class="tb-ticket-id">
                                            <a href="{{ route('tenant.support-tickets.show',$ticket->id) }}">{{ $ticket->ticket_id }}</a>
                                        </td>
                                        <td class="tb-ticket-desc">
                                            <a href="{{ route('tenant.support-tickets.show',$ticket->id) }}"><span class="title">{{ $ticket->subject }}</span>
                                            </a>
                                        </td>
                                        <td class="tb-ticket-date tb-col-md">
                                            <span class="date">{{ $ticket->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td class="tb-ticket-seen tb-col-md">
                                            
                                        </td>
                                        <td class="tb-ticket-status">
                                           @include('admin.tickets.partials.status')
                                        </td>
                                        <td class="tb-ticket-action">
                                            <a href="{{ route('tenant.support-tickets.show',$ticket->id) }}" class="btn btn-icon btn-trigger">
                                                <em class="icon ni ni-chevron-right"></em>
                                            </a>
                                        </td>
                                    </tr><!-- .tb-ticket-item -->
                                @endforeach

                                </tbody>
                            </table>

                            <div class="p-2 text-center my-2">
                                {{ $tickets->links() }}
                            </div>
                        </div>
                    </div><!-- .nk-block -->


                </div>
            </div>
        </div>
    </div>


@endsection
