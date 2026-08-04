{{-- ============================================================================
     Hero — full-bleed muted video behind a masked headline.

     Footage: a 10s aerial of the Rajshahi College building, cut from the
     association's own campus video. The tail is cross-blended into the head so
     the loop point is invisible. To replace it, drop a new hero.mp4 (and
     optionally hero.webm / hero-poster.jpg) into public/media — the sources
     below are emitted only for files that actually exist.
     ========================================================================= --}}
{{-- min-height accounts for the header only; the utility bar above it is
     hidden below the lg breakpoint. --}}
<section data-hero data-theme="dark" data-parallax-scope
         class="relative flex min-h-[calc(100svh-4.6rem)] items-center overflow-hidden bg-ink-950 lg:min-h-[calc(100svh-7.6rem)]">

    {{-- Media plate --}}
    <div data-hero-media class="absolute inset-0">
        <div data-hero-mask class="absolute inset-0 will-change-[clip-path]">
            @php
                // Sources are emitted only for files that actually exist, so the
                // placeholder build doesn't fire 404s. Drop hero.mp4 / hero.webm
                // into public/media and the video takes over on the next load.
                $sources = collect(['webm' => 'video/webm', 'mp4' => 'video/mp4'])
                    ->filter(fn ($mime, $ext) => file_exists(public_path("media/hero.{$ext}")));

                // Real poster when one has been supplied, else the drawn placeholder.
                $poster = asset(file_exists(public_path('media/hero-poster.jpg'))
                    ? 'media/hero-poster.jpg'
                    : 'media/hero-poster.svg');
            @endphp

            @if ($sources->isNotEmpty())
                <video data-hero-video
                       class="absolute inset-0 h-full w-full object-cover brightness-125 saturate-110"
                       autoplay muted loop playsinline preload="metadata"
                       poster="{{ $poster }}" aria-hidden="true" tabindex="-1">
                    @foreach ($sources as $ext => $mime)
                        <source src="{{ asset("media/hero.{$ext}") }}" type="{{ $mime }}">
                    @endforeach
                </video>
            @endif

            {{-- Shown until the video reports `playing`, and permanently if
                 autoplay is refused or no footage has been supplied yet. --}}
            <div data-hero-poster
                 class="absolute inset-0 bg-ink-900 bg-cover bg-center brightness-125 saturate-110 transition-opacity duration-700"
                 style="background-image:url('{{ $poster }}')"></div>
        </div>

        {{-- Legibility scrim, weighted left. The headline sits on the left, the
             college building on the right — so the darkening is heaviest where
             the text is and lightest where the footage is worth seeing. --}}
        {{-- Narrow screens put the text across the full width, so they get an
             even scrim instead of the left-weighted one. --}}
        <div class="absolute inset-0 bg-ink-950/45 md:bg-ink-950/22"></div>
        <div class="absolute inset-0 hidden bg-gradient-to-r from-ink-950/85 via-ink-950/45 to-transparent md:block"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-ink-950/75 via-transparent to-ink-950/30"></div>
        <div class="bg-grid-light absolute inset-0 opacity-25"></div>
    </div>

    {{-- Content --}}
    <div data-hero-content class="container-rc relative z-10 py-24 md:py-28">
        <p class="eyebrow eyebrow-light" data-hero-fade>
            Rajshahi College, est. {{ config('rcmaa.college_founded') }}
        </p>

        <h1 class="heading-display mt-7 max-w-[19ch] text-[clamp(2.4rem,5.6vw,4.6rem)] text-parchment"
            data-hero-title>
            Rajshahi College Mathematics Alumni Association
        </h1>

        <p class="mt-8 max-w-xl text-[1.05rem] leading-relaxed text-ink-200" data-hero-fade>
            {{ config('rcmaa.tagline') }}
            <span class="text-brass-400">Honoring our heritage, fostering our future.</span>
        </p>

        <div class="mt-10 flex flex-wrap items-center gap-4" data-hero-fade>
            <a href="{{ route('register.create') }}" class="btn btn-primary">
                Join the Association
                <x-icon name="arrow-right" class="h-4 w-4"/>
            </a>
            <a href="{{ route('directory') }}" class="btn btn-outline-light">View Directory</a>
        </div>

        {{-- Countdown — days, hours and minutes, as the specification asks. --}}
        @php
            $eventDate = \Carbon\Carbon::parse(config('rcmaa.registration.event_date'));
            $hasNotPassed = $eventDate->isFuture();
        @endphp
        @if ($hasNotPassed)
            <div class="mt-14 flex flex-wrap items-center gap-x-8 gap-y-5 border-t border-white/12 pt-8"
                 data-hero-fade x-data="countdown('{{ $eventDate->toIso8601String() }}')">

                <div class="flex items-start gap-5">
                    @foreach ([['days', 'Days'], ['hours', 'Hours'], ['minutes', 'Minutes']] as [$unit, $label])
                        <div class="text-center">
                            <span class="heading-display block text-4xl leading-none text-brass-500 tabular-nums"
                                  x-text="{{ $unit === 'days' ? 'days' : "pad({$unit})" }}">0</span>
                            <span class="mt-1.5 block font-mono text-[0.6rem] uppercase tracking-[0.18em] text-ink-400">
                                {{ $label }}
                            </span>
                        </div>
                        @unless ($loop->last)
                            <span class="heading-display pt-0.5 text-3xl leading-none text-ink-600">:</span>
                        @endunless
                    @endforeach

                    <span class="self-center pl-2 font-mono text-[0.66rem] uppercase leading-relaxed tracking-[0.2em] text-ink-300">
                        to the<br>Grand Reunion
                    </span>
                </div>

                <span class="hidden h-10 w-px bg-white/12 sm:block"></span>
                <div class="flex items-center gap-3 text-sm text-ink-200">
                    <x-icon name="calendar" class="h-4 w-4 text-brass-500"/>
                    {{ $eventDate->format('l, j F Y') }}
                </div>
                <span class="hidden h-10 w-px bg-white/12 sm:block"></span>
                <div class="flex items-center gap-3 text-sm text-ink-200">
                    <x-icon name="map-pin" class="h-4 w-4 text-brass-500"/>
                    Rajshahi College Campus
                </div>
            </div>
        @endif
    </div>

    {{-- Scroll cue --}}
    <a href="#about" class="absolute bottom-8 left-1/2 z-10 hidden -translate-x-1/2 flex-col items-center gap-2 md:flex"
       aria-label="Scroll to content">
        <span class="font-mono text-[0.6rem] uppercase tracking-[0.24em] text-ink-400">Scroll</span>
        <span class="relative h-10 w-px overflow-hidden bg-white/15">
            <span class="absolute inset-x-0 top-0 h-1/2 animate-[scrollcue_2s_ease-in-out_infinite] bg-brass-500"></span>
        </span>
    </a>

    @push('head')
        <style>
            @keyframes scrollcue {
                0% { transform: translateY(-100%); }
                60%, 100% { transform: translateY(200%); }
            }
        </style>
    @endpush
</section>
