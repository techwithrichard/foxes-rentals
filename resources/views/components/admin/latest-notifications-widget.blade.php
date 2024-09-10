<div class="card card-bordered card-full">
    <div class="card-inner border-bottom">
        <div class="card-title-group">
            <div class="card-title">
                <h6 class="title">{{ __('Latest Notifications')}}</h6>
            </div>
            <div class="card-tools">
                <ul class="card-tools-nav">
                    <li class="active"><a href="#"><span>{{ __('All')}}</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <ul class="nk-activity">

        @forelse($notifications as $notification)

            <li class="nk-activity-item">
                <div class="nk-activity-media user-avatar bg-google-dim">
                    <em class="icon ni ni-bell"></em>
                </div>
                <div class="nk-activity-data">
                    <div class="label">{{ $notification->data['title'] }}</div>
                    <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
            </li>

        @empty

        @endforelse
    </ul>
</div>
