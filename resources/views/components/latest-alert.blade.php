<div>
    @if($latestAlert)
        <p>{{ \Illuminate\Support\Str::words($latestAlert->data['title'],6) }}
            <span> {{ \Illuminate\Support\Str::words($latestAlert->data['message'],7) }}</span>
        </p>
        @else
        <p>No alerts
        </p>
    @endif
</div>
