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
            @auth('alumni')
                <a href="{{ route('member.dashboard') }}" class="btn btn-primary">
                    My Account
                    <x-icon name="arrow-right" class="h-4 w-4"/>
                </a>
            @else
                <a href="{{ route('register.create') }}" class="btn btn-primary">
                    Join the Association
                    <x-icon name="arrow-right" class="h-4 w-4"/>
                </a>
            @endauth
            <a href="{{ route('directory') }}" class="btn btn-outline-light">View Directory</a>
        </div>

        {{-- Countdown.
             It carries its own dark ground rather than trusting the video
             behind it: the footage moves, and a panel that reads over one frame
             can be illegible over the next. With a ground of its own the brass
             carries perfectly well — it was the video underneath it, not the
             colour, that made it hard to read. --}}
        @php
            $eventDate = \Carbon\Carbon::parse(config('rcmaa.registration.event_date'));
            $hasNotPassed = $eventDate->isFuture();
        @endphp
        @if ($hasNotPassed)
            {{-- Full width while stacked, hugging its content only once there is
                 room. As an inline-flex it took a fixed 338px from the widest
                 row inside it and could not shrink: on a 360px Android it kept
                 its left margin and ran off the right edge, which is what read
                 as the countdown being pushed to one side. --}}
            <div class="mt-12 flex w-full flex-col gap-5 rounded-2xl border border-white/12 bg-ink-950/85 px-4 py-5 shadow-[0_24px_60px_-30px_rgba(0,0,0,.9)] backdrop-blur-md sm:px-7 lg:inline-flex lg:w-auto lg:flex-row lg:items-center lg:gap-8"
                 data-hero-fade x-data="countdown('{{ $eventDate->toIso8601String() }}')">

                {{-- Four equal columns that divide whatever width there is, so the
                     row centres itself and cannot overflow. From sm up the cells
                     go back to their natural size and sit together in the middle. --}}
                <div class="flex w-full items-start justify-between gap-1.5 sm:w-auto sm:justify-center sm:gap-4">
                    @foreach ([['days', 'Days'], ['hours', 'Hours'], ['minutes', 'Minutes'], ['seconds', 'Seconds']] as [$unit, $label])
                        <div class="min-w-0 flex-1 text-center sm:min-w-[3.6rem] sm:flex-none">
                            {{-- Scales with the viewport so three digits of Days
                                 still fit a quarter of a 320px screen. --}}
                            <span class="heading-display block text-[clamp(1.5rem,7vw,2.1rem)] font-semibold leading-none text-brass-500 tabular-nums [text-shadow:0_2px_18px_rgba(0,0,0,.55)] sm:text-[2.6rem]"
                                  x-text="{{ $unit === 'days' ? 'days' : "pad({$unit})" }}">00</span>
                            {{-- -me cancels the trailing letter-space that tracking
                                 adds after the last character, which otherwise
                                 shifts the label left of the number above it. --}}
                            {{-- "SECONDS" plus its tracking is wider than a quarter
                                 of a 320px screen, so both size and tracking ease
                                 off with the viewport. -me cancels the trailing
                                 letter-space, which otherwise shifts the label left
                                 of the number above it. --}}
                            <span class="mt-2 block -me-[0.18em] truncate font-mono text-[clamp(0.44rem,2.4vw,0.58rem)] uppercase tracking-[0.1em] text-ink-300 sm:tracking-[0.18em]">
                                {{ $label }}
                            </span>
                        </div>
                        @unless ($loop->last)
                            <span class="heading-display flex-none pt-0.5 text-xl leading-none text-brass-500/35 sm:text-3xl" aria-hidden="true">:</span>
                        @endunless
                    @endforeach
                </div>

                <div class="border-t border-white/10 pt-4 text-center lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0 lg:text-left">
                    <p class="font-mono text-[0.62rem] uppercase leading-relaxed tracking-[0.2em] text-brass-400">
                        To the Grand Reunion
                    </p>
                    <p class="mt-2 flex items-center justify-center gap-2 text-sm text-ink-200 lg:justify-start">
                        <x-icon name="calendar" class="h-4 w-4 flex-none text-brass-500"/>
                        {{ $eventDate->format('l, j F Y') }}
                    </p>
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
