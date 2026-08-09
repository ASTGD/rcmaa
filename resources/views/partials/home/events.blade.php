<section class="relative bg-parchment py-24 md:py-32">
    <div class="container-rc">
        <div class="grid gap-10 lg:grid-cols-[1fr_auto] lg:items-end">
            <x-section-heading eyebrow="Upcoming Events" title="Bridging Generations Through Mathematical Events"
                lead="Join the Department of Mathematics, Rajshahi College, for our upcoming alumni reunions, seminars, and networking sessions. Experience meaningful discussions, celebrate academic excellence, and reconnect with fellow mathematicians." />

            <a href="{{ route('events.index') }}" class="btn btn-outline flex-none" data-reveal data-reveal-delay="0.2">
                All Events
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        @if ($upcomingEvents->isNotEmpty())
            @php
                $event = $upcomingEvents->first();
            @endphp
            <div class="mt-16 card overflow-hidden border border-ink-900/8 bg-white w-full shadow-md">
                {{-- 1. Full Width Banner --}}
                {{-- The cover is a designed banner: a tagline across the top, the
                     title, the date, the venue. object-cover on a 21/9 frame scaled
                     a 2:1 artwork up to fill the width and took the tagline off the
                     top. The frame matches a 2:1 banner now, and object-contain
                     means an upload of any other proportion is letterboxed rather
                     than having part of its message cut away. --}}
                <div class="relative aspect-[2/1] w-full overflow-hidden bg-ink-900">
                    @if ($event->cover_url)
                        <img src="{{ $event->cover_url }}" alt="{{ $event->title }}" loading="eager"
                            class="h-full w-full object-contain">
                    @else
                        <div class="bg-grid-light grid h-full place-items-center">
                            <x-icon name="sigma" class="h-16 w-16 text-brass-500/60" />
                        </div>
                    @endif

                    {{-- Date chip --}}
                    <div class="absolute top-6 left-6 rounded-xl bg-parchment/95 px-4 py-2.5 text-center backdrop-blur shadow-sm">
                        <p class="heading-display text-2xl leading-none text-ink-950">
                            {{ $event->starts_on->format('d') }}
                        </p>
                        <p class="mt-1 font-mono text-[0.62rem] uppercase tracking-[0.16em] text-brass-700">
                            {{ $event->starts_on->format('M Y') }}
                        </p>
                    </div>

                    @if ($event->registration_open)
                        <span class="absolute top-6 right-6 rounded-full bg-brass-500 px-4 py-1.5 font-mono text-[0.62rem] font-semibold uppercase tracking-[0.14em] text-ink-950 shadow-sm">
                            Open
                        </span>
                    @endif
                </div>

                {{-- Content Body: 2-Column Grid --}}
                <div class="p-8 md:p-10">
                    <div class="grid gap-8 md:grid-cols-2">
                        
                        {{-- Left Column: Countdown & Registration Counts --}}
                        <div class="space-y-6">
                            {{-- Live Countdown Timer --}}
                            <div>
                                <p class="font-mono text-xs uppercase tracking-wider text-brass-700 mb-3">Last Time of Registration</p>
                                <div x-data="{
                                    date: '{{ ($event->registration_deadline ? $event->registration_deadline->format('Y-m-d') : config('rcmaa.registration.deadline')) }} 23:59:59',
                                    days: 0, hours: 0, minutes: 0, seconds: 0,
                                    init() {
                                        const target = new Date(this.date).getTime();
                                        const update = () => {
                                            const now = new Date().getTime();
                                            const diff = target - now;
                                            if (diff > 0) {
                                                this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                                this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                                            }
                                        };
                                        update();
                                        setInterval(update, 1000);
                                    }
                                }" class="flex gap-2 items-center">
                                    <div class="bg-parchment-dim text-ink-900 rounded-xl p-2.5 text-center min-w-[64px] border border-ink-900/5">
                                        <span class="block text-xl font-bold font-mono" x-text="days">0</span>
                                        <span class="text-[0.55rem] uppercase tracking-wider text-ink-500">Days</span>
                                    </div>
                                    <div class="bg-parchment-dim text-ink-900 rounded-xl p-2.5 text-center min-w-[64px] border border-ink-900/5">
                                        <span class="block text-xl font-bold font-mono" x-text="hours">0</span>
                                        <span class="text-[0.55rem] uppercase tracking-wider text-ink-500">Hours</span>
                                    </div>
                                    <div class="bg-parchment-dim text-ink-900 rounded-xl p-2.5 text-center min-w-[64px] border border-ink-900/5">
                                        <span class="block text-xl font-bold font-mono" x-text="minutes">0</span>
                                        <span class="text-[0.55rem] uppercase tracking-wider text-ink-500">Mins</span>
                                    </div>
                                    <div class="bg-parchment-dim text-ink-900 rounded-xl p-2.5 text-center min-w-[64px] border border-ink-900/5">
                                        <span class="block text-xl font-bold font-mono" x-text="seconds">0</span>
                                        <span class="text-[0.55rem] uppercase tracking-wider text-ink-500">Secs</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Category Wise Registered count --}}
                            <div class="border-t border-ink-900/5 pt-4">
                                <h4 class="font-mono text-xs uppercase tracking-wider text-brass-700 mb-3">Registered Alumni by Category</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-parchment-dim rounded-xl p-3 border border-ink-900/5">
                                        <span class="block text-xs text-ink-500">Alumnus</span>
                                        <span class="text-lg font-bold text-ink-950">{{ $categoryCounts->get('alumni', 0) }}</span>
                                    </div>
                                    <div class="bg-parchment-dim rounded-xl p-3 border border-ink-900/5">
                                        <span class="block text-xs text-ink-500">Recent Graduate</span>
                                        <span class="text-lg font-bold text-ink-950">{{ $categoryCounts->get('recent_graduate', 0) }}</span>
                                    </div>
                                    <div class="bg-parchment-dim rounded-xl p-3 border border-ink-900/5">
                                        <span class="block text-xs text-ink-500">Current Student</span>
                                        <span class="text-lg font-bold text-ink-950">{{ $categoryCounts->get('current_student', 0) }}</span>
                                    </div>
                                    <div class="bg-parchment-dim rounded-xl p-3 border border-ink-900/5">
                                        <span class="block text-xs text-ink-500">Teacher</span>
                                        <span class="text-lg font-bold text-ink-950">{{ $categoryCounts->get('teacher', 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Excerpt & Registration Link --}}
                        <div class="flex flex-col justify-between space-y-6">
                            <div>
                                <h3 class="heading-display text-2xl md:text-3xl text-ink-950">
                                    {{ $event->title }}
                                </h3>
                                <div class="mt-3 space-y-1.5 text-sm text-ink-600">
                                    @if ($event->start_time)
                                        <p class="flex items-center gap-2.5">
                                            <x-icon name="clock" class="h-4 w-4 text-brass-600" />
                                            <span>{{ $event->start_time }}</span>
                                        </p>
                                    @endif
                                    @if ($event->venue)
                                        <p class="flex items-center gap-2.5">
                                            <x-icon name="map-pin" class="h-4 w-4 text-brass-600" />
                                            <span>{{ $event->venue }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            @if ($event->excerpt)
                                <div>
                                    <h4 class="font-mono text-xs uppercase tracking-wider text-brass-700 mb-3">About The Event</h4>
                                    <p class="prose-rc text-ink-700 text-base leading-relaxed">
                                        {{ $event->excerpt }}
                                    </p>
                                </div>
                            @endif

                            {{-- Registration link --}}
                            <div class="pt-6 border-t border-ink-900/5">
                                <a href="{{ route('register.create') }}" class="btn btn-primary btn-lg w-full text-center flex items-center justify-center gap-2">
                                    <x-icon name="user-plus" class="h-5 w-5" />
                                    Register for Reunion
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @else
            <x-empty-state class="mt-16" icon="calendar" title="No upcoming events published"
                message="Events added from the admin area will appear here automatically." />
        @endif
    </div>
</section>