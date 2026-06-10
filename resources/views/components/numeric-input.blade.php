@props([
    'decimal' => true,
    'placeholder' => '',
])

@php
    $wireModel = $attributes->wire('model')->value();
@endphp

<input
    type="text"
    inputmode="{{ $decimal ? 'decimal' : 'numeric' }}"
    placeholder="{{ $placeholder }}"
    autocomplete="off"
    wire:model.live="{{ $wireModel }}"
    x-on:keydown="window.blockNumericKey($event, { decimal: @js((bool) $decimal) })"
    x-on:input.capture="window.sanitizeNumericInput($event, { decimal: @js((bool) $decimal) })"
    x-on:blur="window.sanitizeNumericInput($event, { decimal: @js((bool) $decimal) })"
    {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.defer'])->merge(['class' => 'mc-input']) }}
/>
