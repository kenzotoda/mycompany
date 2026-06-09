@props(['value'])

<label {{ $attributes->merge(['class' => 'mc-label']) }}>
    {{ $value ?? $slot }}
</label>
