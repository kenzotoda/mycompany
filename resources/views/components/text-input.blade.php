@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'mc-input']) }}>
