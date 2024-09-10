@props([
	'options' => [],
	'items' => null
])
@props([
	'options' => [],
	'selectedItems' => []
])

<div wire:ignore>
    <select x-data="{
		tomSelectInstance: null,
		options: {{ collect($options) }},
		items: @json($selectedItems),

		renderTemplate(data, escape) {
			return `<div class='flex items-center'>
				<span class='mr-3 w-8 h-8 rounded-full bg-gray-100'><img src='https://avatars.dicebear.com/api/initials/${escape(data.title)}.svg' class='w-8 h-8 rounded-full'/></span>
				<div><span class='block font-medium text-gray-700'>${escape(data.title)}</span>
				<span class='block text-gray-500'>${escape(data.subtitle)}</span></div>
			</div>`;
		},
		itemTemplate(data, escape) {
			return `<div>
				<span class='block font-medium text-gray-700'>${escape(data.title)}</span>
			</div>`;
		}
	}" x-init="tomSelectInstance = new TomSelect($refs.input, {
		valueField: 'id',
		labelField: 'title',
		searchField: 'title',
		options: options,
		items: items,
		@if (! empty($items) && ! $attributes->has('multiple'))
			placeholder: undefined,
		@endif
		render: {
			option: renderTemplate,
			item: itemTemplate
		}
	});" x-ref="input" x-cloak {{ $attributes }} placeholder="Pick some links..."></select>
</div>
@once
    @push('css')
        <style>
            .ts-input {
                padding: 10px 8px;
                border-radius: 0.5rem;
                border-color: rgba(209, 213, 219, 1.0);
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }

            .ts-input.focus {
                outline: 2px solid transparent;
                outline-offset: 2px;
                box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), 0 0 0 3px rgba(199, 210, 254, 0.5);
                border-color: rgba(165, 180, 252, 1.0);
            }

            .ts-input.dropdown-active {
                border-radius: 0.5rem 0.5rem 0 0;
            }

            .ts-dropdown {
                margin: -5px 0 0 0;
                border-radius: 0 0 0.5rem 0.5rem;
                padding-bottom: 4px;
            }

            .ts-control.single .ts-input:after {
                content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='none' viewBox='0 0 24 24' stroke='%239CA3AF'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 9l4-4 4 4m0 6l-4 4-4-4' /%3E%3C/svg%3E");
                display: block;
                position: absolute;
                top: 10px;
                right: 8px;
                width: 24px;
                height: 24px;
                border: none;
            }
        </style>
    @endpush

@endonce
