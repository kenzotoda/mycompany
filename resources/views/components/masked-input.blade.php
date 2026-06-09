@props([
    'mask' => 'cpf',
    'maxlength' => 14,
    'placeholder' => '',
])

@php
    $wireModel = $attributes->wire('model')->value();
@endphp

<input
    type="text"
    inputmode="numeric"
    maxlength="{{ $maxlength }}"
    placeholder="{{ $placeholder }}"
    autocomplete="off"
    wire:ignore
    x-data="{ value: @entangle($wireModel).live }"
    x-bind:value="value"
    x-on:keydown="window.blockMaskKey($event)"
    x-on:input="
        window.applyMask($event, @js($mask));
        value = $event.target.value;
    "
    x-on:paste.prevent="
        const pasted = (event.clipboardData.getData('text') || '').replace(/\D/g, '');
        $event.target.value = pasted;
        window.applyMask($event, @js($mask));
        value = $event.target.value;
    "
    {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.defer'])->merge(['class' => 'mc-input']) }}
/>
