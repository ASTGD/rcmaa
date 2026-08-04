<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Faculty"
        title="The teachers of the department"
        lead="The teaching body of the Department of Mathematics, Rajshahi College — the people behind every session represented in our directory."
        :breadcrumbs="['About' => route('about'), 'Faculty' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc">
            @if ($head)
                <article class="card grid gap-8 overflow-hidden md:grid-cols-[20rem_1fr]" data-reveal>
                    <div class="relative aspect-4/5 bg-ink-800 md:aspect-auto">
                        @if ($head->photo_url)
                            <img src="{{ $head->photo_url }}" alt="{{ $head->name }}"
                                 class="h-full w-full object-cover" loading="lazy">
                        @else
                            <div class="bg-grid-light grid h-full min-h-72 place-items-center">
                                <span class="heading-display text-6xl text-brass-500/70">{{ $head->initials }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-8 md:py-10 md:pr-10">
                        <p class="eyebrow">Head of Department</p>
                        <h2 class="heading-display mt-4 text-3xl text-ink-950">{{ $head->name }}</h2>
                        <p class="mt-1 text-sm text-brass-700">{{ $head->designation }}</p>

                        @if ($head->qualification)
                            <p class="mt-4 text-sm text-ink-500">{{ $head->qualification }}</p>
                        @endif
                        @if ($head->bio)
                            <p class="prose-rc mt-4 text-[0.95rem]">{{ $head->bio }}</p>
                        @endif

                        <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                            @if ($head->email)
                                <a href="mailto:{{ $head->email }}" class="flex items-center gap-2 text-ink-600 transition hover:text-brass-700">
                                    <x-icon name="mail" class="h-4 w-4 text-brass-600"/>{{ $head->email }}
                                </a>
                            @endif
                            @if ($head->phone)
                                <a href="tel:{{ preg_replace('/\D/', '', $head->phone) }}" class="flex items-center gap-2 text-ink-600 transition hover:text-brass-700">
                                    <x-icon name="phone" class="h-4 w-4 text-brass-600"/>{{ $head->phone }}
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endif

            @if ($teachers->isNotEmpty())
                <div class="{{ $head ? 'mt-14' : '' }}">
                    @if ($head)
                        <x-section-heading eyebrow="Faculty" title="Teaching staff" size="sm" class="mb-10"/>
                    @endif

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                         data-reveal data-reveal-stagger="0.08">
                        @foreach ($teachers as $teacher)
                            <x-person-card :person="$teacher" show-contact/>
                        @endforeach
                    </div>
                </div>
            @elseif (! $head)
                <x-empty-state icon="book"
                    title="Faculty list not published yet"
                    message="Add teaching staff from Admin → Faculty and they will appear here."/>
            @endif
        </div>
    </section>


    {{-- Department at a glance — from the college's own published record. --}}
    <section data-theme="dark" class="relative overflow-hidden bg-ink-900 py-20 md:py-28">
        <div class="bg-grid-light pointer-events-none absolute inset-0"></div>

        <div class="container-rc relative">
            <x-section-heading light eyebrow="At a Glance"
                title="The department's own record"
                lead="Mathematics has been taught at Rajshahi College since long before the association existed. These are the department's milestones, as published by the college."/>

            <div class="mt-14 grid gap-6 lg:grid-cols-[1.1fr_1fr] lg:gap-14">
                {{-- Milestones --}}
                <div>
                    <ol class="space-y-0" data-reveal data-reveal-stagger="0.08">
                        @foreach (config('rcmaa.department.milestones') as $year => $what)
                            <li class="flex gap-6 border-t border-white/10 py-5" data-reveal-item>
                                <span class="heading-display flex-none text-3xl text-brass-500">{{ $year }}</span>
                                <span class="self-center text-sm leading-relaxed text-ink-200">{{ $what }}</span>
                            </li>
                        @endforeach
                    </ol>

                    <div class="mt-8 grid grid-cols-2 gap-4" data-reveal>
                        @foreach ([
                            [config('rcmaa.department.teachers'), 'Teaching posts'],
                            [config('rcmaa.department.students'), 'Students'],
                        ] as [$value, $label])
                            <div class="rounded-2xl border border-white/10 bg-ink-800/50 p-5">
                                <p class="heading-display text-2xl text-parchment">{{ $value }}</p>
                                <p class="mt-1 text-xs text-ink-400">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Former heads --}}
                <div x-data="{ all: false }">
                    <p class="eyebrow eyebrow-light">Heads of Department</p>

                    @php $heads = collect(config('rcmaa.department.former_heads'))->reverse()->values(); @endphp

                    <ul class="mt-6 space-y-0">
                        @foreach ($heads as $i => [$name, $period])
                            <li @class(['border-t border-white/8', 'hidden' => $i >= 5])
                                @if ($i >= 5) x-show="all" x-collapse.duration.400ms @endif>
                                <div class="flex items-baseline justify-between gap-5 py-3">
                                    <span class="text-sm text-ink-200">{{ $name }}</span>
                                    <span class="flex-none font-mono text-[0.68rem] text-brass-400">{{ $period }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <button type="button" @click="all = !all"
                            class="mt-5 inline-flex items-center gap-2 text-[0.76rem] font-semibold uppercase tracking-[0.1em] text-brass-400 transition hover:text-parchment">
                        <span x-text="all ? 'Show fewer' : 'Show all {{ $heads->count() }} since 1912'"></span>
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 transition-transform" ::class="all && 'rotate-180'"/>
                    </button>

                    <div class="mt-8 rounded-2xl border border-white/10 bg-ink-800/50 p-5">
                        <p class="font-mono text-[0.6rem] uppercase tracking-[0.18em] text-brass-400">Department contact</p>
                        <a href="mailto:{{ config('rcmaa.department.email') }}"
                           class="mt-3 flex items-center gap-2.5 text-sm text-ink-200 transition hover:text-parchment">
                            <x-icon name="mail" class="h-4 w-4 flex-none text-brass-500"/>{{ config('rcmaa.department.email') }}
                        </a>
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.department.phone')) }}"
                           class="mt-2 flex items-center gap-2.5 text-sm text-ink-200 transition hover:text-parchment">
                            <x-icon name="phone" class="h-4 w-4 flex-none text-brass-500"/>{{ config('rcmaa.department.phone') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.cta')
</x-layout>
