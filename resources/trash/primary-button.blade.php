@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => 'inline-flex items-center px-6 py-2 bg-button-primary rounded-full font-semibold text-lg text-button-primary-text text-lg tracking-widest shadow-sm hover:bg-button-primary-hover focus:outline-none focus:ring-2 focus:ring-input-focus focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-button-primary border border-border rounded-md font-semibold text-xs text-button-primary-text uppercase tracking-widest shadow-sm hover:bg-button-primary-hover focus:outline-none focus:ring-2 focus:ring-input-focus focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
        {{ $slot }}
    </button>
@endif