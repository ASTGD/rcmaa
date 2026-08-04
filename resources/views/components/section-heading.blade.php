@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'align' => 'left',
    'light' => false,
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'text-[clamp(1.5rem,2.6vw,2rem)]',
        'md' => 'text-[clamp(1.9rem,3.6vw,3rem)]',
        'lg' => 'text-[clamp(2.3rem,5vw,4rem)]',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'max-w-3xl '.($align === 'center' ? 'mx-auto text-center' : '')]) }}>
    @if ($eyebrow)
        <p class="eyebrow {{ $light ? 'eyebrow-light' : '' }} {{ $align === 'center' ? 'justify-center' : '' }}"
           data-reveal data-reveal-delay="0.05">
            {{ $eyebrow }}
        </p>
    @endif

    @if ($title)
        <h2 class="heading-display mt-5 {{ $sizes[$size] }} {{ $light ? 'text-parchment' : 'text-ink-950' }}"
            data-reveal="split">
            {{ $title }}
        </h2>
    @endif

    @if ($lead)
        <p class="prose-rc mt-5 text-[1.02rem] {{ $light ? '!text-ink-200' : '' }} {{ $align === 'center' ? 'mx-auto' : '' }}"
           data-reveal data-reveal-delay="0.18">
            {{ $lead }}
        </p>
    @endif

    {{ $slot }}
</div>
