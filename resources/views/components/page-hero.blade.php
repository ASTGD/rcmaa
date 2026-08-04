@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'breadcrumbs' => [],
])

{{-- Compact dark banner used by every page except the home page. --}}
<section data-theme="dark" class="relative overflow-hidden bg-ink-900 pt-20 pb-16 md:pt-28 md:pb-24">
    <div class="bg-grid-light pointer-events-none absolute inset-0"></div>
    <div class="pointer-events-none absolute -top-32 -right-24 h-[26rem] w-[26rem] rounded-full bg-brass-700/18 blur-[110px]"></div>
    <div class="pointer-events-none absolute -bottom-40 -left-20 h-[22rem] w-[22rem] rounded-full bg-ink-500/25 blur-[110px]"></div>

    <div class="container-rc relative">
        @if ($breadcrumbs)
            <nav aria-label="Breadcrumb" data-reveal data-reveal-delay="0.05">
                <ol class="flex flex-wrap items-center gap-2 text-[0.72rem] text-ink-400">
                    <li><a href="{{ route('home') }}" class="transition hover:text-brass-400">Home</a></li>
                    @foreach ($breadcrumbs as $label => $url)
                        <li aria-hidden="true" class="text-ink-600">/</li>
                        <li>
                            @if ($url)
                                <a href="{{ $url }}" class="transition hover:text-brass-400">{{ $label }}</a>
                            @else
                                <span class="text-brass-400">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @if ($eyebrow)
            <p class="eyebrow eyebrow-light mt-8" data-reveal data-reveal-delay="0.1">{{ $eyebrow }}</p>
        @endif

        <h1 class="heading-display mt-5 max-w-4xl text-[clamp(2.2rem,5.4vw,4.2rem)] text-parchment" data-reveal="split">
            {{ $title }}
        </h1>

        @if ($lead)
            <p class="prose-rc mt-6 max-w-2xl text-[1.02rem] !text-ink-200" data-reveal data-reveal-delay="0.2">
                {{ $lead }}
            </p>
        @endif

        {{ $slot }}
    </div>
</section>
