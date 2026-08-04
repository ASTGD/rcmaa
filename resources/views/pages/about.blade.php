{{--
    The origin story below is the association's own, condensed from the Bangla
    "Our goal" page of rcmaa.bd (page 389); the full account with dates lives on
    /our-goal. The opening paragraph is verbatim from the old home page.

    The old /about/ page itself was never written — it still carried the theme's
    "Universite" demo text — so there is nothing further to carry across.
--}}
<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="About"
        title="A community built on one shared classroom"
        lead="{{ config('rcmaa.tagline') }} Honoring our heritage, fostering our future."
        :breadcrumbs="['About' => null]"/>

    {{-- Story --}}
    <section class="bg-grid bg-parchment py-20 md:py-28" data-parallax-scope>
        <div class="container-rc grid gap-14 lg:grid-cols-[1fr_1.05fr] lg:items-center lg:gap-20">
            <div class="relative overflow-hidden rounded-3xl" data-reveal="mask">
                <img src="{{ Storage::disk('public')->url('gallery/classroom.jpg') }}"
                     alt="A teacher of the Department of Mathematics addressing a meeting in the classroom"
                     class="aspect-4/5 w-full object-cover" data-parallax="-0.05" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-ink-950/45 to-transparent"></div>
            </div>

            <div>
                <x-section-heading
                    eyebrow="Our Story"
                    title="Welcome to the Rajshahi College Mathematics Alumni Association"/>

                <div class="prose-rc mt-6" data-reveal data-reveal-delay="0.15">
                    <p>
                        Founded with a legacy of academic excellence, the Rajshahi College Mathematics
                        Alumni Association brings together decades of graduates under one global
                        community. RCMAA is a space to reconnect, collaborate, and inspire the next
                        generation of mathematicians. Together, we celebrate our shared journey, create
                        meaningful opportunities, and shape the future of our alma mater.
                    </p>
                    <p>
                        For many years the Department of Mathematics at Rajshahi College has helped shape
                        able students, teachers, researchers, officers of public and private institutions,
                        professionals and established citizens across Bangladesh. But over time, contact
                        between the department's past and present students began to fall away.
                    </p>
                    <p>
                        Out of that reality, a group of enterprising former and current students conceived
                        a platform that would bind every member of the department in a common bond. The
                        association's journey began on <strong>16 December 2025</strong> and took formal
                        shape at a meeting on the campus on <strong>3 January 2026</strong>, with students
                        from the 2011-12 through 2024-25 sessions taking part.
                    </p>
                </div>

                <div class="mt-9 flex flex-wrap gap-4" data-reveal data-reveal-delay="0.25">
                    <a href="{{ route('our-goal') }}" class="btn btn-ink">Read the Full History</a>
                    <a href="{{ route('committee') }}" class="btn btn-outline">Meet the Committee</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Two dates worth keeping straight --}}
    <section class="bg-parchment-dim py-16">
        <div class="container-rc grid gap-6 md:grid-cols-2" data-reveal data-reveal-stagger="0.12">
            @foreach ([
                [config('rcmaa.college_founded'), 'Rajshahi College', 'One of the oldest institutions in the subcontinent, and the home of the Department of Mathematics.'],
                [config('rcmaa.founded'), 'RCMAA', 'The alumni association itself — constituted in January 2026 and now preparing its first grand reunion.'],
            ] as [$year, $heading, $body])
                <article class="card flex items-start gap-6 p-8" data-reveal-item>
                    <p class="heading-display flex-none text-5xl text-brass-500" data-count="{{ $year }}">0</p>
                    <div>
                        <h2 class="heading-display text-lg text-ink-950">{{ $heading }}</h2>
                        <p class="prose-rc mt-2 text-sm">{{ $body }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- What we do --}}
    <section data-theme="dark" class="relative overflow-hidden bg-ink-900 py-20 md:py-28">
        <div class="bg-grid-light pointer-events-none absolute inset-0"></div>

        <div class="container-rc relative">
            <x-section-heading
                light align="center"
                eyebrow="What We Do"
                title="Four things the association is for"/>

            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal data-reveal-stagger="0.1">
                @foreach ([
                    ['users', 'Reconnect', 'Re-establish the contact between past and present students that had been lost, and hold it in a session-wise database.'],
                    ['graduation', 'Support students', 'Stand beside talented and financially struggling students, and share knowledge and advice between senior and junior years.'],
                    ['calendar', 'Convene', 'Reunions, seminars and discussion meetings that bring the department and its graduates into the same room each year.'],
                    ['heart', 'Give back', 'A coordinated structure to help students of the department in medical or emergency need.'],
                ] as [$icon, $heading, $body])
                    <article class="rounded-2xl border border-white/8 bg-ink-800/50 p-7 transition-colors duration-500 hover:border-brass-600/40 hover:bg-ink-800"
                             data-reveal-item>
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-brass-500/12 text-brass-400">
                            <x-icon :name="$icon" class="h-5 w-5"/>
                        </span>
                        <h3 class="heading-display mt-6 text-lg text-parchment">{{ $heading }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-ink-300">{{ $body }}</p>
                    </article>
                @endforeach
            </div>

            <p class="mt-12 text-center" data-reveal>
                <a href="{{ route('our-goal') }}" class="btn btn-outline-light">
                    All six aims and seven objectives
                    <x-icon name="arrow-right" class="h-4 w-4"/>
                </a>
            </p>
        </div>
    </section>

    {{-- Department statement, verbatim from the old site --}}
    <section class="bg-parchment py-20 md:py-28">
        <div class="container-narrow text-center">
            <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-brass-500 text-ink-950" data-reveal="scale">
                <x-icon name="quote" class="h-5 w-5"/>
            </span>

            <blockquote class="heading-display mt-8 text-[clamp(1.35rem,2.8vw,2.1rem)] text-ink-950" data-reveal="split">
                The Department of Mathematics at Rajshahi College is committed to nurturing analytical
                thinking, research, and academic leadership.
            </blockquote>

            <p class="prose-rc mx-auto mt-6 max-w-2xl" data-reveal data-reveal-delay="0.2">
                We empower our students with strong foundational knowledge and problem-solving skills to
                excel in higher education and diverse professional careers worldwide.
            </p>
        </div>
    </section>

    @include('partials.cta')
</x-layout>
