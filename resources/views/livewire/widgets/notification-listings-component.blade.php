<div>
    <div class="nk-block">
        <div class="card card-bordered h-100">
            <div class="card-inner border-bottom">
                <div class="card-title-group">
                    <div class="card-title">
                        <h6 class="title">{{ __('All Unread Notifications')}}</h6>
                    </div>
                    <div class="card-tools">
                        <a href="javascript:void(0);" wire:click.prevent="markAllAsRead" class="link">
                            {{ __('Mark All As Read')}}
                        </a>
                    </div>
                </div>
            </div>
            <ul class="nk-support">
                @forelse($notifications as $notification)
                    <li class="nk-support-item">
                        <div class="nk-notification-icon">
                            <em class="icon icon-circle bg-success-dim ni ni-curve-down-right"></em>
                        </div>
                        <div class="nk-support-content ">
                            <div class="title">
                                <span>{{ $notification->data['title'] }}</span>
                                <div class="status unread">
                                    <a href="javascript:void(0);" class="link-cross" wire:click="markAsRead('{{$notification->id}}')"
                                       >
                                        {{ __('Clear')}}
                                    </a>
                                </div>
                            </div>
                            <p>{{ $notification->data['message'] }}</p>
                            <span class="time">{{  $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </li>
                @empty
                    <div class="p-2">
                        <div class="alert alert-primary alert-icon">
                            <em class="icon ni ni-alert-circle"></em> <strong>{{ __('No New Notifications')}}</strong>.
                            {{ __('You have read all your notifications.')}}
                        </div>
                    </div>


                @endforelse

            </ul>
        </div>
    </div>
</div>
