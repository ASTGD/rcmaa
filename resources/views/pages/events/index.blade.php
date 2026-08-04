<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Events"
        title="Bridging generations through mathematics"
        lead="Join the Department of Mathematics, Rajshahi College, for our upcoming alumni reunions, seminars, and networking sessions. Experience meaningful discussions, celebrate academic excellence, and reconnect with fellow mathematicians."
        :breadcrumbs="['Events' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc">
            @if ($upcoming->isNotEmpty())
                <x-section-heading eyebrow="Upcoming" title="What's next" size="sm"/>

                <div class="mt-10 space-y-5" data-reveal data-reveal-stagger="0.1">
                    @foreach ($upcoming as $event)
                        <a href="{{ route('events.show', $event) }}"
                           class="card card-hover group grid gap-6 overflow-hidden md:grid-cols-[16rem_1fr] lg:grid-cols-[22rem_1fr]"
                           data-reveal-item>
                            <div class="relative aspect-16/9 overflow-hidden bg-ink-800 md:aspect-auto md:min-h-56">
                                @if ($event->cover_url)
                                    <img src="{{ $event->cover_url }}" alt="{{ $event->title }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-[900ms] ease-[cubic-bezier(.22,1,.36,1)] group-hover:scale-[1.05]">
                                @else
                                    <div class="bg-grid-light grid h-full place-items-center">
                                        <x-icon name="sigma" class="h-10 w-10 text-brass-500/60"/>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col justify-center p-6 md:py-8 md:pr-8">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="rounded-full bg-ink-900 px-3 py-1 font-mono text-[0.6rem] uppercase tracking-[0.14em] text-brass-400">
                                        {{ $event->starts_on->format('j M Y') }}
                                    </span>
                                    @if ($event->registration_open)
                                        <span class="rounded-full bg-brass-500 px-3 py-1 font-mono text-[0.6rem] font-semibold uppercase tracking-[0.14em] text-ink-950">
                                            Registration open
                                        </span>
                                    @endif
                                    @if ($event->countdown_days > 0)
                                        <span class="text-xs text-ink-400">in {{ $event->countdown_days }} days</span>
                                    @endif
                                </div>

                                <h3 class="heading-display mt-4 text-2xl text-ink-950 transition-colors group-hover:text-brass-700">
                                    {{ $event->title }}
                                </h3>

                                @if ($event->excerpt)
                                    <p class="prose-rc mt-3 max-w-2xl text-[0.95rem]">{{ $event->excerpt }}</p>
                                @endif

                                <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-[0.8rem] text-ink-500">
                                    @if ($event->start_time)
                                        <span class="flex items-center gap-2"><x-icon name="clock" class="h-3.5 w-3.5 text-brass-600"/>{{ $event->start_time }}</span>
                                    @endif
                                    @if ($event->venue)
                                        <span class="flex items-center gap-2"><x-icon name="map-pin" class="h-3.5 w-3.5 text-brass-600"/>{{ $event->venue }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <x-empty-state icon="calendar" title="No upcoming events"
                    message="There are no scheduled events at the moment. Check the notice board for announcements."/>
            @endif

            @if ($past->isNotEmpty())
                <div class="mt-20">
                    <x-section-heading eyebrow="Archive" title="Past events" size="sm"/>

                    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-reveal data-reveal-stagger="0.07">
                        @foreach ($past as $event)
                            <a href="{{ route('events.show', $event) }}"
                               class="card card-hover group p-6" data-reveal-item>
                                <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-brass-700">
                                    {{ $event->starts_on->format('j M Y') }}
                                </p>
                                <h3 class="heading-display mt-3 text-lg text-ink-950 transition-colors group-hover:text-brass-700">
                                    {{ $event->title }}
                                </h3>
                                @if ($event->venue)
                                    <p class="mt-3 flex items-center gap-2 text-[0.8rem] text-ink-500">
                                        <x-icon name="map-pin" class="h-3.5 w-3.5 text-brass-600"/>{{ $event->venue }}
                                    </p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    @include('partials.cta')
</x-layout>
