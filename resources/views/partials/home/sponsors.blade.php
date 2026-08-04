<section class="border-t border-ink-900/8 bg-parchment py-20 md:py-24">
    <div class="container-rc">
        <x-section-heading
            align="center"
            eyebrow="Partners"
            title="Our sponsors for the Grand Reunion 2026"
            size="sm"
            lead="We are deeply grateful to our sponsors and corporate partners whose generous support makes this grand reunion possible. Their commitment plays a vital role in celebrating the legacy of the Rajshahi College Mathematics Department and empowering our community."/>

        @if ($sponsors->isNotEmpty())
            <div class="relative mt-14 overflow-hidden mask-fade-x" data-marquee="45">
                <div class="flex w-max items-center gap-16" data-marquee-track>
                    @foreach ($sponsors as $sponsor)
                        <a @if ($sponsor->website) href="{{ $sponsor->website }}" target="_blank" rel="noopener noreferrer" @endif
                           class="flex-none opacity-45 grayscale transition-all duration-500 hover:opacity-100 hover:grayscale-0"
                           title="{{ $sponsor->name }} — {{ $sponsor->tier_label }}">
                            @if ($sponsor->logo_url)
                                <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->name }}"
                                     class="h-10 w-auto object-contain" loading="lazy">
                            @else
                                <span class="heading-display whitespace-nowrap text-xl text-ink-700">{{ $sponsor->name }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-12 text-center" data-reveal>
                <p class="prose-rc text-sm">Sponsorship packages for the Grand Reunion 2026 are open.</p>
                <a href="{{ route('contact') }}" class="btn btn-outline btn-sm mt-5">Become a Sponsor</a>
            </div>
        @endif
    </div>

    @push('head')
        <style>
            .mask-fade-x {
                -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
                mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
            }
        </style>
    @endpush
</section>
