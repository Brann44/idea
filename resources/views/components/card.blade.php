@props(['href' => null, 'is' => 'div'])

@php
    $tag = $href ? 'a' : $is;
    $classes = 'border border-border rounded-lg bg-card p-4 md:text-sm' . ($href ? ' block hover:border-foreground/20 transition-colors' : '');
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>
