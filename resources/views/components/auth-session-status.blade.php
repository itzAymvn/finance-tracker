@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-emerald']) }}>
        {{ $status }}
    </div>
@endif
