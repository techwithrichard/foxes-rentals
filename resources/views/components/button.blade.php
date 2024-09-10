@props(['loading' => false])

@php
    $target=$attributes->wire('click')->value();
@endphp
<div>
    <button {{ $attributes->merge(['type' => 'submit','class'=>'btn']) }} wire:loading.attr="disabled"
            id="{{ uniqid()}}">
        <div wire:loading wire:target="{{$target}}">
            <span class="spinner-border spinner-border-sm hide" role="status" aria-hidden="true" wire:loading
                  wire:target="{{$target}}"></span>
        </div>
        @if($loading!==false)
            <div wire:loading wire:target="{{$target}}">
                <span wire:loading wire:target="{{$target}}">{{ $loading }}</span>
            </div>
            <div wire:loading.remove wire:target="{{$target}}">
                <span wire:loading.remove wire:target="{{$target}}">{{ $slot }}</span>
            </div>

        @else
            {{ $slot }}

        @endif


    </button>
</div>
