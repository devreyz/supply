@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-border bg-input text-input-on focus:border-primary focus:ring-primary focus:bg-input-focus valid:border-success/10 rounded-md shadow-sm']) !!}>
