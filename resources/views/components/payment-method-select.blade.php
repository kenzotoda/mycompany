@props(['disabled' => false])

<x-searchable-select {{ $attributes }} :disabled="$disabled">
    @foreach (\App\Support\PaymentMethods::options() as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</x-searchable-select>
