@props(['active', 'icon' => null])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-2 border-l-4 border-brand-orange bg-zinc-900 py-2 pe-4 ps-3 text-base font-medium text-white'
    : 'flex items-center gap-2 border-l-4 border-transparent py-2 pe-4 ps-3 text-base font-medium text-zinc-400 hover:border-zinc-600 hover:bg-zinc-900 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <i class="fa-solid {{ $icon }} w-4 text-center text-sm"></i>
    @endif
    {{ $slot }}
</a>
