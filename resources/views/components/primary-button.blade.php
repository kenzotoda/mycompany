<button {{ $attributes->merge(['type' => 'submit', 'class' => 'mc-btn-primary']) }}>
    {{ $slot }}
</button>
