<div class="card card-bordered h-100">
    <div class="card-inner border-bottom">
        <div class="card-title-group">
            <div class="card-title">
                <h6 class="title">{{ __('Support Requests')}}</h6>
            </div>
            <div class="card-tools">
                <a href="{{ route('admin.support-tickets.index') }}" class="link">{{ __('All Requests')}}</a>
            </div>
        </div>
    </div>
    <ul class="nk-support">
        @forelse($latestSupportTickets as $ticket)
            <li class="nk-support-item">
                <div class="user-avatar bg-purple-dim">
                    <span>{{ $ticket->user->initials }}</span>
                </div>
                <div class="nk-support-content ">
                    <div class="title">
                        <span>{{ $ticket->user->name }}</span>
                        <div class="status unread">
                            <em class="icon ni ni-bullet-fill"></em>
                        </div>
                    </div>
                    <p>{{ $ticket->subject }}</p>
                    <span class="time">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
            </li>

            @empty
            <div class="example-alert p-2">
                <div class="alert alert-primary alert-icon">
                    <em class="icon ni ni-alert-circle"></em> <strong>
                        {{ __('No Support Requests Available') }}
                    </strong>
                </div>
            </div>
        @endforelse
    </ul>
</div>

