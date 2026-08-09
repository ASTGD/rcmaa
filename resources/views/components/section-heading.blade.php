@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'align' => 'left',
    'light' => false,
    'size' => 'md',
    /*
     * Scroll-reveal the heading, or render it plainly.
     *
     * The reveal hides its target until a scroll trigger fires. That is fine
     * for decoration, but a section whose whole point is content people are
     * waiting to see should not depend on an animation running at all — pass
     * :reveal="false" and it is simply there.
     */
    'reveal' => true,
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
           @if ($reveal) data-reveal data-reveal-delay="0.05" @endif>
            {{ $eyebrow }}
        </p>
    @endif

    @if ($title)
        <h2 class="heading-display mt-5 {{ $sizes[$size] }} {{ $light ? 'text-parchment' : 'text-ink-950' }}"
            @if ($reveal) data-reveal="split" @endif>
            {{ $title }}
        </h2>
    @endif

    @if ($lead)
        <p class="prose-rc mt-5 text-[1.02rem] {{ $light ? '!text-ink-200' : '' }} {{ $align === 'center' ? 'mx-auto' : '' }}"
           @if ($reveal) data-reveal data-reveal-delay="0.18" @endif>
            {{ $lead }}
        </p>
    @endif

    {{ $slot }}
</div>
