@props(['value'])

<label class='block font-medium text-sm text-text-secondary '>
    {{ $value ?? $slot }}
</label>
