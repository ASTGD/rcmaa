@props([
    // Deliberately not named `class` — a prop of that name swallows the caller's
    // HTML class attribute, which silently drops the sizing utilities.
    'size' => 'h-11 w-11',
    'wordmark' => true,
    'light' => false,
])

@php
    // The header darkens as it passes over a dark section, and the wordmark has
    // to follow or the name goes dark-on-dark and stops being readable. Each
    // utility carries its own variant — a prefix in front of a two-class string
    // only scopes the first, which is how a stray colour escaped once before.
    $ink = $light
        ? 'text-parchment'
        : 'text-ink-900 [.is-over-dark_&]:text-parchment';

    $sub = $light
        ? 'text-brass-400'
        : 'text-brass-700 [.is-over-dark_&]:text-brass-400';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    {{-- The association's own seal. Its lettering is illegible below ~120px, so
         the wordmark beside it carries the name at nav sizes. --}}
    <img src="{{ asset('media/logo.png') }}"
         alt="{{ config('rcmaa.name') }}"
         class="{{ $size }} flex-none object-contain"
         width="288" height="275" loading="eager" decoding="async">

    @if ($wordmark)
        <span class="flex flex-col leading-none">
            <span class="heading-display text-[1.28rem] font-semibold tracking-tight {{ $ink }}">RCMAA</span>
            <span class="mt-1 font-mono text-[0.5rem] uppercase tracking-[0.24em] {{ $sub }}">
                Rajshahi College &middot; Mathematics
            </span>
        </span>
    @endif
</span>
