@extends('layouts.main')


@push('css')
    <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>

@endpush

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-content-wrap">
                        <div class="nk-block-head">
                            <div class="nk-block-between g-3">
                                <div class="nk-block-head-content">
                                    <h3 class="nk-block-title page-title">{{__('Ticket Details')}}
                                        <span>
                                         @include('admin.tickets.partials.status')
                                    </span>
                                    </h3>
                                </div>
                                <div class="nk-block-head-content">

                                <x-back_link href="{{ route('admin.support-tickets.index') }}"></x-back_link>
                                  

                                </div>
                            </div>
                        </div><!-- .nk-block-head -->

                        @if(session()->has('success'))
                            <div class="nk-block">
                                <div class="example-alert">
                                    <div class="alert alert-pro alert-success alert-icon"><em
                                            class="icon ni ni-alert-circle"></em>
                                        <strong>{{ session()->get('success') }}
                                        </strong>
                                    </div>
                                </div>

                            </div>
                        @endif



                    <!--New Ticket Details -->
                        <div class="nk-block">
                            <div class="card card-bordered">
                                <div class="card-inner border-bottom bg-lighter py-3">
                                    <div class="d-sm-flex align-items-sm-center justify-content-sm-between">
                                        <div class="pb-1 pb-sm-0">
                                            <h5 class="title">{{ $ticket->subject }}</h5>
                                            <div class="d-sm-flex">
                                                <span class="m-0 pe-2">by <a
                                                        href="#">{{ $ticket->user->name }}</a></span>
                                                <span>{{ $ticket->created_at->format('d M Y h:i s') }}</span>
                                            </div>

                                        </div>
                                        @livewire('admin.ticket.update-ticket-status-component',['ticket'=>$ticket])

                                    </div>
                                </div>
                                <div class="card-inner">
                                    <p class="text-soft">  {!! $ticket->message !!}</p>

                                    @foreach($ticket->attachments as $attachment1)
                                        <a href="{{ url($attachment1->path) }}" download class="fw-bolder fs-12px">
                                            <em class="icon text-info ni ni-clip-h"></em>
                                            {{ $attachment1->name }}
                                        </a>
                                    @endforeach


                                </div>

                                @foreach($ticket->replies as $reply)
                                    <div class="card-inner border-top">
                                        <div class="d-flex">
                                            <div
                                                class="user-avatar {{ $reply->user_id == auth()->id()?'bg-success':'bg-danger' }} me-3">
                                                <span>{{ $reply->user->initials }}</span>
                                            </div>
                                            <div class="fake-class">
                                                <h6 class="mt-0 d-flex align-center">
                                                    <span>{{ $reply->user->name }}</span>

                                                    @if($reply->user_id == auth()->id())
                                                        <span class="badge badge-dim bg-outline-info ms-2">You</span>
                                                    @endif
                                                </h6>
                                                <p class="text-soft">
                                                    {{ $reply->message }}
                                                </p>

                                                @foreach($reply->attachments as $attachment)
                                                    <a href="{{ url($attachment->path) }}" download
                                                       class="fw-bolder fs-12px">
                                                        <em class="icon text-info ni ni-clip-h"></em>
                                                        {{ $attachment->name }}
                                                    </a>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div><!-- .card -->
                        </div>

                        @livewire('admin.ticket.reply-ticket-component', ['ticketId' => $ticket->id])
                        


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

