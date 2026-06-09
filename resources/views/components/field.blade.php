@props(['label' => null, 'for' => null, 'required' => false])

<div {{ $attributes->merge(['class' => 'mc-field']) }}>
    @if ($label)
        <label @if($for) for="{{ $for }}" @endif class="mc-label">
            {{ $label }}@if($required) <span class="text-brand-orange">*</span>@endif
        </label>
    @endif
    {{ $slot }}
</div>
