@props(['href' => null])

@php
    $classes = 'border border-border rounded-lg bg-card p-4 md:text-sm' . ($href ? ' block hover:border-foreground/20 transition-colors' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </div>
@endif
