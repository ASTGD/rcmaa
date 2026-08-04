<section class="bg-parchment py-24 md:py-32">
    <div class="container-rc">
        <div class="grid gap-10 lg:grid-cols-[1fr_auto] lg:items-end">
            <x-section-heading
                eyebrow="Gallery"
                title="Our Story"
                lead="A glimpse into the moments, milestones, and collective efforts that bring our alumni community together."/>

            <a href="{{ route('gallery') }}" class="btn btn-outline flex-none" data-reveal data-reveal-delay="0.2">
                View More
                <x-icon name="arrow-right" class="h-4 w-4"/>
            </a>
        </div>

        @if ($galleryItems->isNotEmpty())
            {{-- Editorial mosaic: first tile spans two rows on wide screens. --}}
            <div class="mt-16 grid auto-rows-[15rem] grid-cols-2 gap-4 lg:grid-cols-4"
                 data-reveal data-reveal-stagger="0.07">
                @foreach ($galleryItems as $index => $item)
                    <a href="{{ route('gallery') }}"
                       class="group relative overflow-hidden rounded-2xl bg-ink-800 {{ $index === 0 ? 'col-span-2 row-span-2' : '' }}"
                       data-reveal-item>
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy"
                             class="h-full w-full object-cover transition-transform duration-[1000ms] ease-[cubic-bezier(.22,1,.36,1)] group-hover:scale-[1.07]">

                        <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/20 to-transparent opacity-80 transition-opacity duration-500 group-hover:opacity-95"></div>

                        <div class="absolute inset-x-0 bottom-0 translate-y-2 p-5 opacity-0 transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100">
                            <p class="font-mono text-[0.58rem] uppercase tracking-[0.18em] text-brass-400">
                                {{ $item->category_label }}
                            </p>
                            <h3 class="mt-1.5 text-sm font-semibold text-parchment">{{ $item->title }}</h3>
                            @if ($item->caption)
                                <p class="mt-1 line-clamp-2 text-[0.75rem] leading-snug text-ink-300">{{ $item->caption }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <x-empty-state class="mt-16" icon="camera"
                title="No photos published yet"
                message="Upload photographs from the admin area and they will appear in this mosaic."/>
        @endif
    </div>
</section>
