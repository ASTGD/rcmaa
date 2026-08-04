@php
    // Serialised for the Alpine lightbox so it can page through images.
    $payload = $items->map(fn ($i) => [
        'id' => $i->id,
        'title' => $i->title,
        'caption' => $i->caption,
        'category' => $i->category,
        'categoryLabel' => $i->category_label,
        'url' => $i->image_url,
    ])->values();
@endphp

<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Gallery"
        title="Our Story"
        lead="A glimpse into the moments, milestones, and collective efforts that bring our alumni community together."
        :breadcrumbs="['Gallery' => null]">
        <p lang="bn" class="prose-rc mt-4 max-w-2xl font-bangla !text-ink-300" data-reveal data-reveal-delay="0.25">
            আমাদের প্রাক্তনী পরিবারকে ঐক্যবদ্ধ করার অবিস্মরণীয় মুহূর্ত, গৌরবময় অর্জন এবং সম্মিলিত প্রচেষ্টার এক পলক
        </p>
    </x-page-hero>

    <section class="bg-parchment py-16 md:py-24"
             x-data="gallery({{ Js::from($payload) }})"
             @keydown.window="onKey($event)">
        <div class="container-rc">

            @if ($items->isNotEmpty())
                {{-- Filters --}}
                @if (count($categories) > 1)
                    <nav aria-label="Gallery filters" class="flex flex-wrap gap-2" data-reveal>
                        <button type="button" @click="setFilter('all')"
                                class="rounded-full px-4 py-2.5 text-[0.8rem] font-medium transition-all duration-300"
                                :class="filter === 'all' ? 'bg-ink-900 text-parchment' : 'bg-white text-ink-600 ring-1 ring-ink-900/8 hover:bg-brass-100'">
                            All ({{ $items->count() }})
                        </button>
                        @foreach ($categories as $key => $label)
                            <button type="button" @click="setFilter('{{ $key }}')"
                                    class="rounded-full px-4 py-2.5 text-[0.8rem] font-medium transition-all duration-300"
                                    :class="filter === '{{ $key }}' ? 'bg-ink-900 text-parchment' : 'bg-white text-ink-600 ring-1 ring-ink-900/8 hover:bg-brass-100'">
                                {{ $label }} ({{ $items->where('category', $key)->count() }})
                            </button>
                        @endforeach
                    </nav>
                @endif

                {{-- Grid --}}
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <template x-for="(item, index) in filtered" :key="item.id">
                        <button type="button" @click="show(index)" data-gallery-item
                                class="group relative aspect-4/3 overflow-hidden rounded-2xl bg-ink-800 text-left">
                            <img :src="item.url" :alt="item.title" loading="lazy"
                                 class="h-full w-full object-cover transition-transform duration-[1000ms] ease-[cubic-bezier(.22,1,.36,1)] group-hover:scale-[1.07]">

                            <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/10 to-transparent opacity-75 transition-opacity duration-500 group-hover:opacity-95"></div>

                            <div class="absolute inset-x-0 bottom-0 translate-y-1.5 p-5 opacity-0 transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100">
                                <p class="font-mono text-[0.58rem] uppercase tracking-[0.18em] text-brass-400"
                                   x-text="item.categoryLabel"></p>
                                <h2 class="mt-1.5 text-sm font-semibold text-parchment" x-text="item.title"></h2>
                            </div>

                            <span class="absolute top-4 right-4 grid h-9 w-9 place-items-center rounded-full bg-parchment/90 opacity-0 backdrop-blur transition-opacity duration-300 group-hover:opacity-100">
                                <x-icon name="search" class="h-4 w-4 text-ink-900"/>
                            </span>
                        </button>
                    </template>
                </div>

                {{-- Lightbox --}}
                <div x-show="open" x-cloak x-transition.opacity.duration.250ms
                     {{-- Backdrop dismissal uses .self rather than click.outside on the
                          figure: click.outside would fire on the same click that opened
                          the lightbox and close it again immediately. --}}
                     @click.self="close()"
                     class="fixed inset-0 z-[70] flex items-center justify-center bg-ink-950/95 p-4 backdrop-blur-sm"
                     role="dialog" aria-modal="true" :aria-label="current?.title">

                    <button type="button" @click="close()"
                            class="absolute top-5 right-5 grid h-11 w-11 place-items-center rounded-full border border-white/15 text-parchment transition hover:bg-white/10"
                            aria-label="Close gallery viewer">
                        <x-icon name="x" class="h-5 w-5"/>
                    </button>

                    <button type="button" @click="previous()"
                            class="absolute left-4 grid h-12 w-12 place-items-center rounded-full border border-white/15 text-parchment transition hover:bg-white/10 md:left-8"
                            aria-label="Previous photo">
                        <x-icon name="chevron-left" class="h-5 w-5"/>
                    </button>

                    <button type="button" @click="next()"
                            class="absolute right-4 grid h-12 w-12 place-items-center rounded-full border border-white/15 text-parchment transition hover:bg-white/10 md:right-8"
                            aria-label="Next photo">
                        <x-icon name="chevron-right" class="h-5 w-5"/>
                    </button>

                    <figure class="max-h-full w-full max-w-4xl">
                        <img :src="current?.url" :alt="current?.title"
                             class="max-h-[70vh] w-full rounded-2xl object-contain">
                        <figcaption class="mt-5 text-center">
                            <p class="font-mono text-[0.6rem] uppercase tracking-[0.2em] text-brass-400"
                               x-text="current?.categoryLabel"></p>
                            <h2 class="heading-display mt-2 text-xl text-parchment" x-text="current?.title"></h2>
                            <p class="mx-auto mt-2 max-w-xl text-sm text-ink-300" x-text="current?.caption"></p>
                            <p class="mt-4 font-mono text-[0.65rem] text-ink-500">
                                <span x-text="index + 1"></span> / <span x-text="filtered.length"></span>
                            </p>
                        </figcaption>
                    </figure>
                </div>
            @else
                <x-empty-state icon="camera" title="No photos published yet"
                    message="Upload photographs from Admin → Gallery and they will appear here."/>
            @endif
        </div>
    </section>

    @include('partials.cta')
</x-layout>
