<section class="relative bg-parchment py-24 md:py-32">
    <div class="container-rc">
        <div class="grid gap-10 lg:grid-cols-[1fr_auto] lg:items-end">
            <x-section-heading
                eyebrow="Upcoming Events"
                title="Bridging generations through mathematics"
                lead="Join the Department of Mathematics, Rajshahi College, for our upcoming alumni reunions, seminars, and networking sessions. Experience meaningful discussions, celebrate academic excellence, and reconnect with fellow mathematicians."/>

            <a href="{{ route('events.index') }}" class="btn btn-outline flex-none" data-reveal data-reveal-delay="0.2">
                All Events
                <x-icon name="arrow-right" class="h-4 w-4"/>
            </a>
        </div>

        @if ($upcomingEvents->isNotEmpty())
            <div class="mt-16 grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-reveal data-reveal-stagger="0.1">
                @foreach ($upcomingEvents as $event)
                    <a href="{{ route('events.show', $event) }}"
                       class="card card-hover group flex flex-col overflow-hidden" data-reveal-item>
                        <div class="relative aspect-16/9 overflow-hidden bg-ink-800">
                            @if ($event->cover_url)
                                <img src="{{ $event->cover_url }}" alt="{{ $event->title }}" loading="lazy"
                                     class="h-full w-full object-cover transition-transform duration-[900ms] ease-[cubic-bezier(.22,1,.36,1)] group-hover:scale-[1.05]">
                            @else
                                <div class="bg-grid-light grid h-full place-items-center">
                                    <x-icon name="sigma" class="h-10 w-10 text-brass-500/60"/>
                                </div>
                            @endif

                            {{-- Date chip --}}
                            <div class="absolute top-4 left-4 rounded-xl bg-parchment/95 px-3.5 py-2 text-center backdrop-blur">
                                <p class="heading-display text-xl leading-none text-ink-950">{{ $event->starts_on->format('d') }}</p>
                                <p class="mt-0.5 font-mono text-[0.58rem] uppercase tracking-[0.16em] text-brass-700">
                                    {{ $event->starts_on->format('M Y') }}
                                </p>
                            </div>

                            @if ($event->registration_open)
                                <span class="absolute top-4 right-4 rounded-full bg-brass-500 px-3 py-1 font-mono text-[0.58rem] font-semibold uppercase tracking-[0.14em] text-ink-950">
                                    Open
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <h3 class="heading-display text-xl text-ink-950 transition-colors group-hover:text-brass-700">
                                {{ $event->title }}
                            </h3>

                            @if ($event->excerpt)
                                <p class="prose-rc mt-3 line-clamp-3 text-sm">{{ $event->excerpt }}</p>
                            @endif

                            <div class="mt-auto space-y-2 pt-5 text-[0.8rem] text-ink-500">
                                @if ($event->start_time)
                                    <p class="flex items-center gap-2">
                                        <x-icon name="clock" class="h-3.5 w-3.5 text-brass-600"/>{{ $event->start_time }}
                                    </p>
                                @endif
                                @if ($event->venue)
                                    <p class="flex items-center gap-2">
                                        <x-icon name="map-pin" class="h-3.5 w-3.5 text-brass-600"/>{{ $event->venue }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <x-empty-state class="mt-16" icon="calendar"
                title="No upcoming events published"
                message="Events added from the admin area will appear here automatically."/>
        @endif
    </div>
</section>
