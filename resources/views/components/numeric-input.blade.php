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
    wire:ignore
    x-data="{ value: @entangle($wireModel).live }"
    x-bind:value="value"
    x-on:keydown="window.blockNumericKey($event, { decimal: @js($decimal) })"
    x-on:input="
        window.sanitizeNumericInput($event, { decimal: @js($decimal) });
        value = $event.target.value;
    "
    x-on:paste.prevent="
        const pasted = (event.clipboardData.getData('text') || '').trim();
        $event.target.value = pasted;
        window.sanitizeNumericInput($event, { decimal: @js($decimal) });
        value = $event.target.value;
    "
    {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.defer'])->merge(['class' => 'mc-input']) }}
/>
