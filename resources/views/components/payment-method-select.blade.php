@props(['disabled' => false])

<select {{ $attributes->merge(['class' => 'mc-input']) }} @disabled($disabled)>
    @foreach (\App\Support\PaymentMethods::options() as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>
