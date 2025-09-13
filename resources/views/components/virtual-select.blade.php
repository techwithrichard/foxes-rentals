<div
    x-data="{ 
        options: @entangle($attributes['options']), 
        selectValue: @entangle($attributes->whereStartsWith('wire:model')->first()),
        selectInstance: null
    }"
    x-init="
        $nextTick(() => {
            if (typeof VirtualSelect !== 'undefined') {
                selectInstance = VirtualSelect.init({
                    ele: $refs.select,
                    options: options,
                    hasOptionDescription: true,
                    search: true,
                    placeholder: 'Select',
                    noOptionsText: 'No results found',
                    maxWidth: '100%'
                });
                
                if (selectValue) {
                    selectInstance.setValue(selectValue);
                }
                
                selectInstance.addEventListener('change', () => {
                    if ([null, undefined, ''].includes(selectInstance.value)) {
                        return;
                    }
                    $wire.set('{{ $attributes->whereStartsWith('wire:model')->first() }}', selectInstance.value);
                });
                
                $watch('options', () => {
                    if (selectInstance) {
                        selectInstance.setOptions(options);
                    }
                });
            } else {
                console.error('VirtualSelect is not loaded');
            }
        })
    ">
    <div x-ref="select" wire:ignore {{ $attributes }}></div>
</div>
