@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 text-sm font-medium text-primary border-b-2 border-primary transition-colors duration-150'
            : 'inline-flex items-center px-1 pt-1 text-sm font-medium text-ink-soft hover:text-ink border-b-2 border-transparent hover:border-ink-soft transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
