@if(array_key_exists('attributes', $properties))
    @foreach($properties['attributes'] as $key => $value)
        <p class="mt-0 mb-0">{{ $key }} : {{ $value }}</p>
    @endforeach
@endif




