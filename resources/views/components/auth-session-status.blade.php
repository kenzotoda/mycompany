@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'mc-alert-success']) }}>
        <i class="fa-solid fa-circle-check mr-2"></i>{{ $status }}
    </div>
@endif
