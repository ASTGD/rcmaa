{{--
    আমাদের ঐতিহ্য · Our Heritage — the college's historical milestones, supplied
    by the association. Bangla is authoritative; English is a translation.
--}}
<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="আমাদের ঐতিহ্য · Our Heritage"
        title="Historical milestones"
        :breadcrumbs="['About' => route('about'), 'Heritage' => null]">
        <p lang="bn" class="mt-4 font-bangla text-xl text-brass-400" data-reveal data-reveal-delay="0.15">
            ঐতিহাসিক মাইলফলক
        </p>
        <p lang="bn" class="prose-rc mt-4 max-w-2xl font-bangla !text-ink-200" data-reveal data-reveal-delay="0.2">
            প্রায় দুই শতাব্দীব্যাপী শিক্ষার প্রতি অবিচল প্রতিশ্রুতি ও শ্রেষ্ঠত্ব
        </p>
        <p class="prose-rc mt-2 max-w-2xl !text-ink-400" data-reveal data-reveal-delay="0.25">
            Close to two centuries of steadfast commitment to education, and to excellence.
        </p>
    </x-page-hero>

    @php
        $milestones = collect(config('heritage'));
        $eras = [
            'Foundation · 1873—1904' => fn ($m) => $m['year'] <= 1904,
            'Growth · 1909—1936' => fn ($m) => $m['year'] > 1904 && $m['year'] <= 1936,
            'Nation · 1952—1994' => fn ($m) => $m['year'] > 1936 && $m['year'] <= 1994,
            'Today · 2026' => fn ($m) => $m['year'] > 1994,
        ];
    @endphp

    <section class="bg-grid bg-parchment py-16 md:py-24">
        <div class="container-rc">
            {{-- Jump rail --}}
            <nav class="mb-14 flex flex-wrap gap-2" aria-label="Eras" data-reveal>
                @foreach ($eras as $label => $filter)
                    <a href="#{{ Str::slug($label) }}"
                       class="rounded-full bg-white px-4 py-2.5 text-[0.78rem] font-medium text-ink-600 ring-1 ring-ink-900/8 transition hover:bg-brass-100 hover:text-ink-950">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            @foreach ($eras as $label => $filter)
                @php $group = $milestones->filter($filter)->values(); @endphp
                @continue($group->isEmpty())

                <div id="{{ Str::slug($label) }}" class="{{ ! $loop->first ? 'mt-20' : '' }} scroll-mt-28">
                    <p class="eyebrow" data-reveal>{{ $label }}</p>

                    <ol class="mt-8">
                        @foreach ($group as $m)
                            <li class="group relative flex gap-6 sm:gap-10" data-reveal>
                                {{-- Rail --}}
                                <div class="relative flex flex-none flex-col items-center">
                                    <span class="mt-2 h-3 w-3 flex-none rounded-full bg-brass-500 ring-4 ring-brass-500/15
                                                 transition-transform duration-500 group-hover:scale-125"></span>
                                    @unless ($loop->last && $loop->parent->last)
                                        <span class="mt-2 w-px flex-1 bg-ink-900/12"></span>
                                    @endunless
                                </div>

                                <div class="min-w-0 pb-12">
                                    <p class="heading-display text-3xl leading-none text-brass-600">{{ $m['year'] }}</p>

                                    <h2 lang="bn" class="mt-3 font-bangla text-lg font-semibold text-ink-950">
                                        {{ $m['heading_bn'] }}
                                    </h2>
                                    <p class="text-[0.86rem] font-medium text-brass-700">{{ $m['heading'] }}</p>

                                    @if ($m['body_bn'])
                                        <p lang="bn" class="mt-3 max-w-2xl font-bangla text-[0.92rem] leading-[1.9] text-ink-600">
                                            {{ $m['body_bn'] }}
                                        </p>
                                    @endif
                                    @if ($m['body'])
                                        <p class="mt-2 max-w-2xl border-l-2 border-brass-500/40 pl-4 text-[0.86rem] leading-relaxed text-ink-400">
                                            {{ $m['body'] }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endforeach
        </div>
    </section>

    @include('partials.cta')
</x-layout>
