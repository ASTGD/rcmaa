@props(['title', 'updated' => null, 'intro' => null, 'sections'])

{{-- Shared shell for Privacy Policy and Terms of Service. --}}
<x-layout :title="$title">
    <x-page-hero
        eyebrow="Legal"
        :title="$title"
        :lead="$intro"
        :breadcrumbs="[$title => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-narrow">
            @if ($updated)
                <p class="font-mono text-[0.66rem] uppercase tracking-[0.18em] text-brass-700" data-reveal>
                    Last updated {{ $updated }}
                </p>
            @endif

            <div class="mt-8 space-y-10">
                @foreach ($sections as $heading => $paragraphs)
                    <section data-reveal>
                        <h2 class="heading-display text-xl text-ink-950">{{ $heading }}</h2>
                        <div class="prose-rc mt-3 text-[0.95rem]">
                            @foreach ((array) $paragraphs as $paragraph)
                                @if (is_array($paragraph))
                                    <ul class="mt-3 space-y-2">
                                        @foreach ($paragraph as $item)
                                            <li class="flex gap-3">
                                                <span class="mt-2 h-1 w-1 flex-none rounded-full bg-brass-600"></span>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="card mt-14 p-6" data-reveal>
                <p class="text-sm text-ink-600">
                    Questions about this page? Write to
                    <a href="mailto:{{ config('rcmaa.contact.email') }}"
                       class="font-medium text-brass-700 underline underline-offset-2">{{ config('rcmaa.contact.email') }}</a>
                    or call {{ config('rcmaa.contact.hotline') }}.
                </p>
            </div>
        </div>
    </section>
</x-layout>
