@props([
    'disabled' => false,
])

<div class="mc-select2-wrap w-full">
    <select
        data-searchable-select
        @disabled($disabled)
        {{ $attributes->merge(['class' => 'form-select']) }}
    >
        {{ $slot }}
    </select>
</div>
