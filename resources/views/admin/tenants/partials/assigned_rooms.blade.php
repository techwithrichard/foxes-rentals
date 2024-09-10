@forelse($leased_houses as $house)
    <span>{{ $house }}</span><br>

@empty
    <span class="text-danger">{{ __('No Active Lease')}}</span>
@endforelse

