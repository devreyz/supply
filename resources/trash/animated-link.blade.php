@props(['url' => 'inicio', 'name' => 'Link'])

@php
$isActive = request()->routeIs($url) ? 'text-primary font-bold after:w-full' : '';
@endphp

<a href="{{ route($url) }}"
    class="text-header-on hover:text-secondary relative after:content-[''] after:absolute after:left-0 after:-bottom-1 after:w-0 after:h-0.5 after:bg-primary after:transition-all after:duration-300 hover:after:w-full hover:after:left-0 focus:after:w-full focus:after:bg-input-focus active:after:bg-secondary {{ $isActive }}">
    {{ $name }}
</a>