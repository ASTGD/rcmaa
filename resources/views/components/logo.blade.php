@props([
    // Deliberately not named `class` — a prop of that name swallows the caller's
    // HTML class attribute, which silently drops the sizing utilities.
    'size' => 'h-11 w-11',
    'wordmark' => true,
    'light' => false,
])

@php
    $ink = $light ? 'text-parchment' : 'text-ink-900';
    $sub = $light ? 'text-brass-400' : 'text-brass-700';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    {{-- The association's own seal. Its lettering is illegible below ~120px, so
         the wordmark beside it carries the name at nav sizes. --}}
    <img src="{{ asset('media/logo.png') }}"
         alt="{{ config('rcmaa.name') }}"
         class="{{ $size }} flex-none object-contain"
         width="512" height="512" loading="eager" decoding="async">

    @if ($wordmark)
        <span class="flex flex-col leading-none">
            <span class="heading-display text-[1.28rem] font-semibold tracking-tight {{ $ink }}">RCMAA</span>
            <span class="mt-1 font-mono text-[0.5rem] uppercase tracking-[0.24em] {{ $sub }}">
                Rajshahi College &middot; Mathematics
            </span>
        </span>
    @endif
</span>
