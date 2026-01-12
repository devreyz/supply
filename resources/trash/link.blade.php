@props([
'href' => '#',
'target' => '_self',
'color' => 'blue',
'size' => 'md',
'outline' => false
])

@php
$baseClasses = "inline-flex items-center justify-center font-semibold transition duration-300 rounded-full focus:outline-none focus:ring";
$sizeClasses = match($size) {
'sm' => 'px-3 py-1 text-sm',
'lg' => 'px-6 py-3 text-lg',
default => 'px-4 py-2 text-base'
};

$colorClasses = $outline
? " text-primary hover:bg-secondary hover:text-secondary-on focus:ring-primary"
: "bg-secondary text-secondary-on hover:bg-primary focus:ring-primary";
@endphp

<a href="{{ $href }}" target="{{ $target }}" class="{{ $baseClasses }} {{ $sizeClasses }} {{ $colorClasses }}">
  {{ $slot }}
</a>