@props(['label' => null, 'for' => null, 'required' => false])

<div {{ $attributes->merge(['class' => 'mc-field']) }}>
    @if ($label)
        <label @if($for) for="{{ $for }}" @endif class="mc-label">
            {{ $label }}@if($required) <span class="text-brand-orange">*</span>@endif
        </label>
    @endif
    {{ $slot }}
    <p class="mc-field-error mt-1 hidden text-xs text-red-600" data-frontend-error role="alert"></p>
</div>
