<div class="dropdown-menu dropdown-menu-xl dropdown-menu-end dropdown-menu-s1">
    <div class="dropdown-head">
        <span class="sub-title nk-dropdown-title">{{ __('Notifications')}}</span>
        <a href="javascript:void(0);" wire:click.prevent="markAllNotificationsAsRead">{{ __('Mark All as Read')}}</a>
    </div>
    <div class="dropdown-body">
        <div class="nk-notification">

            @forelse($notifications as $notification)
                <div class="nk-notification-item dropdown-inner">
                    <div class="nk-notification-icon">
                        <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                    </div>
                    <div class="nk-notification-content">
                        <div class="nk-notification-text">{{ $notification->data['title'] }}</div>
                        <div class="nk-notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                </div>

            @empty
                <div class="nk-notification-item dropdown-inner">
                    <div class="nk-notification-content">
                        <div class="nk-notification-text">{{ __('You are all caught up.')}}</div>
                    </div>
                </div>

            @endforelse

        </div><!-- .nk-notification -->
    </div><!-- .nk-dropdown-body -->
    <div class="dropdown-foot center">
        <a href="{{ route('notifications') }}">{{ __('View All')}}</a>
    </div>
</div>
