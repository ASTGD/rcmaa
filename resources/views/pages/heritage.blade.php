{{--
    আমাদের ঐতিহ্য · Our Heritage — the Department of Mathematics' own timeline,
    supplied by the association. Bangla is authoritative; English is a
    translation.

    The association's ordering interleaves each mathematics milestone with the
    college background around it, so entries are rendered in the order given
    rather than sorted by year. The era rail this page used to carry has gone
    with the old date-ordered list — it would have reordered their sequence.
--}}
<x-layout :title="$title" :description="$description">
    @php
        $heritage = config('heritage');
        $timeline = collect($heritage['timeline']);

        // Styling per entry kind. The association marks its own milestones apart
        // from the college context that surrounds them.
        $kinds = [
            'math' => [
                'label' => 'গণিত বিভাগ',
                'label_en' => 'Mathematics milestone',
                'dot' => 'bg-brass-500 ring-brass-500/20',
                'year' => 'text-brass-600',
                'chip' => 'bg-brass-100 text-brass-800 ring-brass-600/20',
            ],
            'background' => [
                'label' => 'ব্যাকগ্রাউন্ড',
                'label_en' => 'College background',
                'dot' => 'bg-ink-300 ring-ink-900/8',
                'year' => 'text-ink-400',
                'chip' => 'bg-ink-900/5 text-ink-500 ring-ink-900/10',
            ],
            'grand' => [
                'label' => 'RCMAA',
                'label_en' => 'The association',
                'dot' => 'bg-brass-500 ring-brass-500/30',
                'year' => 'text-brass-600',
                'chip' => 'bg-brass-500 text-ink-950 ring-brass-600/30',
            ],
        ];
    @endphp

    <x-page-hero
        eyebrow="আমাদের ঐতিহ্য · Our Heritage"
        :title="$heritage['title']"
        :breadcrumbs="['About' => route('about'), 'Heritage' => null]">
        <p lang="bn" class="mt-4 font-bangla text-xl text-brass-400" data-reveal data-reveal-delay="0.15">
            {{ $heritage['title_bn'] }}
        </p>
        <p lang="bn" class="prose-rc mt-4 max-w-2xl font-bangla !text-ink-200" data-reveal data-reveal-delay="0.2">
            {{ $heritage['subtitle_bn'] }}
        </p>
        <p class="prose-rc mt-2 max-w-2xl !text-ink-400" data-reveal data-reveal-delay="0.25">
            {{ $heritage['subtitle'] }}
        </p>
    </x-page-hero>

    <section class="bg-grid bg-parchment py-16 md:py-24">
        <div class="container-rc">

            {{-- What the two kinds of entry mean, so the rail reads without explanation. --}}
            <ul class="mb-14 flex flex-wrap items-center gap-x-6 gap-y-3" data-reveal>
                @foreach (['math', 'background'] as $key)
                    <li class="flex items-center gap-2.5">
                        <span class="h-2.5 w-2.5 flex-none rounded-full ring-4 {{ $kinds[$key]['dot'] }}"></span>
                        <span lang="bn" class="font-bangla text-[0.85rem] text-ink-700">{{ $kinds[$key]['label'] }}</span>
                        <span class="text-[0.75rem] text-ink-400">{{ $kinds[$key]['label_en'] }}</span>
                    </li>
                @endforeach
            </ul>

            <ol>
                @foreach ($timeline as $m)
                    @php $k = $kinds[$m['kind']] ?? $kinds['background']; @endphp
                    <li class="group relative flex gap-6 sm:gap-10" data-reveal>
                        {{-- Rail --}}
                        <div class="relative flex flex-none flex-col items-center">
                            <span class="mt-2 h-3 w-3 flex-none rounded-full ring-4 {{ $k['dot'] }}
                                         transition-transform duration-500 group-hover:scale-125"></span>
                            @unless ($loop->last)
                                <span class="mt-2 w-px flex-1 bg-ink-900/12"></span>
                            @endunless
                        </div>

                        <div class="min-w-0 pb-12">
                            <div class="flex flex-wrap items-baseline gap-x-4 gap-y-2">
                                <p class="heading-display text-3xl leading-none {{ $k['year'] }}">{{ $m['year'] }}</p>
                                <span lang="bn" class="font-bangla text-sm text-ink-400">{{ $m['year_bn'] }}</span>
                            </div>

                            <p class="mt-3">
                                <span class="inline-block rounded-full px-2.5 py-1 font-mono text-[0.6rem] uppercase tracking-[0.14em] ring-1 {{ $k['chip'] }}">
                                    {{ $m['category'] }}
                                </span>
                            </p>

                            <h2 lang="bn" class="mt-3 font-bangla text-lg font-semibold text-ink-950">
                                {{ $m['heading_bn'] }}
                            </h2>
                            <p class="text-[0.86rem] font-medium text-brass-700">{{ $m['heading'] }}</p>

                            <p lang="bn" class="mt-3 max-w-2xl font-bangla text-[0.92rem] leading-[1.9] text-ink-600">
                                {{ $m['body_bn'] }}
                            </p>
                            <p class="mt-2 max-w-2xl border-l-2 border-brass-500/40 pl-4 text-[0.86rem] leading-relaxed text-ink-400">
                                {{ $m['body'] }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    @include('partials.cta')
</x-layout>
