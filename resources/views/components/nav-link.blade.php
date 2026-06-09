@props(['active', 'icon' => null])

@php
$classes = ($active ?? false)
    ? 'mc-nav-link-active'
    : 'mc-nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <i class="mc-nav-icon fa-solid {{ $icon }}"></i>
    @endif
    {{ $slot }}
</a>
