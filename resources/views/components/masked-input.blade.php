@props([
    'mask' => 'cpf',
    'maxlength' => 14,
    'placeholder' => '',
    'storeDigits' => false,
])

@php
    $wireModel = $attributes->wire('model')->value();
    $inputClass = $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.defer'])->get('class', 'mc-input');
@endphp

@if ($storeDigits)
    <input
        type="text"
        inputmode="numeric"
        maxlength="{{ $maxlength }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        data-mask="{{ $mask }}"
        data-wire-model="{{ $wireModel }}"
        data-store-digits
        wire:ignore
        x-data="{
            digits: @entangle($wireModel).live,
            mask: @js($mask),
            syncDisplay() {
                this.$el.value = window.formatByMask(this.mask, this.digits);
            },
            updateFromRaw(raw) {
                this.digits = window.digitsFromMask(this.mask, raw);
                this.syncDisplay();
            },
        }"
        x-init="$nextTick(() => syncDisplay())"
        x-effect="syncDisplay()"
        x-on:keydown="window.blockMaskKey($event)"
        x-on:input="window.applyMask($event, mask); updateFromRaw($event.target.value)"
        x-on:paste="$nextTick(() => updateFromRaw($el.value))"
        x-on:blur="window.applyMask($event, mask); updateFromRaw($event.target.value)"
        {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.defer', 'class'])->merge(['class' => $inputClass]) }}
    />
@else
    <input
        type="text"
        inputmode="numeric"
        maxlength="{{ $maxlength }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        data-mask="{{ $mask }}"
        data-wire-model="{{ $wireModel }}"
        wire:ignore
        x-data="{ value: @entangle($wireModel).live }"
        x-model="value"
        x-init="$nextTick(() => { if (value) { window.applyMask({ target: $el }, @js($mask)); } })"
        x-on:keydown="window.blockMaskKey($event)"
        x-on:input="
            window.applyMask($event, @js($mask));
            value = $event.target.value;
        "
        x-on:paste="$nextTick(() => { window.applyMask({ target: $el }, @js($mask)); value = $el.value; })"
        x-on:blur="
            window.applyMask($event, @js($mask));
            value = $event.target.value;
        "
        {{ $attributes->except(['wire:model', 'wire:model.live', 'wire:model.blur', 'wire:model.defer', 'class', 'storeDigits', 'store-digits'])->merge(['class' => $inputClass]) }}
    />
@endif
