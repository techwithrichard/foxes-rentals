<a {{ $attributes->merge(['class' => 'btn btn-outline-light bg-white']) }}>

    @if(app()->getLocale()=='ar')
        <span>{{ __('Back')}}</span>
        <em class="icon ni ni-arrow-right"></em>
    @else
        <em class="icon ni ni-arrow-left"></em>
        <span>{{ __('Back')}}</span>
    @endif


</a>
