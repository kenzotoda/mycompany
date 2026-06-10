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
    data-mask="{{ $mask }}"
    wire:model.live="{{ $wireModel }}"
    x-init="$nextTick(() => window.applyMask({ target: $el }, @js($mask)))"
    x-on:keydown="window.blockMaskKey($event)"
    x-on:input.capture="window.applyMask($event, @js($mask))"
    x-on:paste="window.formatAfterPaste($event, @js($mask))"
    x-on:blur="window.applyMask($event, @js($mask))"
    {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.defer'])->merge(['class' => 'mc-input']) }}
/>
