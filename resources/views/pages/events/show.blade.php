<x-layout :title="$title" :description="$description">
    <x-page-hero
        :eyebrow="$event->starts_on->format('j F Y')"
        :title="$event->title"
        :lead="$event->excerpt"
        :breadcrumbs="['Events' => route('events.index'), $event->title => null]">

        <div class="mt-9 flex flex-wrap items-center gap-x-8 gap-y-3 text-sm text-ink-300" data-reveal data-reveal-delay="0.3">
            @if ($event->start_time)
                <span class="flex items-center gap-2"><x-icon name="clock" class="h-4 w-4 text-brass-500"/>{{ $event->start_time }}</span>
            @endif
            @if ($event->venue)
                <span class="flex items-center gap-2"><x-icon name="map-pin" class="h-4 w-4 text-brass-500"/>{{ $event->venue }}</span>
            @endif
            @if ($event->countdown_days > 0)
                <span class="flex items-center gap-2"><x-icon name="calendar" class="h-4 w-4 text-brass-500"/>{{ $event->countdown_days }} days to go</span>
            @endif
        </div>

        @if ($event->registration_open)
            <div class="mt-8" data-reveal data-reveal-delay="0.35">
                @auth('alumni')
                    <a href="{{ route('member.dashboard') }}" class="btn btn-primary">
                        My Account / Dashboard
                        <x-icon name="arrow-right" class="h-4 w-4"/>
                    </a>
                @else
                    <a href="{{ route('register.create') }}" class="btn btn-primary">
                        Register for this Event
                        <x-icon name="arrow-right" class="h-4 w-4"/>
                    </a>
                @endauth
            </div>
        @endif
    </x-page-hero>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-narrow">
            @if ($event->cover_url)
                {{-- Contained, not covered: a poster's own proportions are part of
                     it, and 16/9 would have trimmed the sides off a 2:1 banner. --}}
                <img src="{{ $event->cover_url }}" alt="{{ $event->title }}"
                     class="mb-12 aspect-[2/1] w-full rounded-2xl bg-ink-900 object-contain" data-reveal="mask">
            @endif

            @if ($event->body)
                <div class="prose-rc text-[1.02rem]" data-reveal>
                    @foreach (preg_split('/\n{2,}/', trim($event->body)) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @endif

            @if ($related->isNotEmpty())
                <div class="mt-16 border-t border-ink-900/10 pt-10">
                    <p class="eyebrow">Also coming up</p>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        @foreach ($related as $other)
                            <a href="{{ route('events.show', $other) }}" class="card card-hover group p-5">
                                <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-brass-700">
                                    {{ $other->starts_on->format('j M Y') }}
                                </p>
                                <h3 class="heading-display mt-2 text-base text-ink-950 transition-colors group-hover:text-brass-700">
                                    {{ $other->title }}
                                </h3>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-layout>
