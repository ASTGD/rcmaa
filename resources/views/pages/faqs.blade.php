<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Support"
        title="Frequently asked questions"
        lead="Answers about membership, reunion registration, payment and events. If yours isn't here, the helpdesk is a phone call away."
        :breadcrumbs="['FAQs' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-narrow">
            @forelse ($grouped as $category => $faqs)
                <div class="{{ ! $loop->first ? 'mt-14' : '' }}">
                    <p class="eyebrow" data-reveal>
                        {{ \App\Models\Faq::CATEGORIES[$category] ?? $category }}
                    </p>

                    <div class="mt-6 space-y-3" x-data="{ open: null }">
                        @foreach ($faqs as $faq)
                            <div class="card overflow-hidden" data-reveal>
                                <button type="button" @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}"
                                        class="flex w-full items-center justify-between gap-5 px-6 py-5 text-left"
                                        :aria-expanded="open === {{ $faq->id }}">
                                    <span class="text-[0.95rem] font-semibold text-ink-900">{{ $faq->question }}</span>
                                    <span class="grid h-8 w-8 flex-none place-items-center rounded-full bg-ink-900/5 transition-transform duration-300"
                                          :class="open === {{ $faq->id }} && 'rotate-45 bg-brass-500'">
                                        <x-icon name="plus" class="h-4 w-4 text-ink-700"/>
                                    </span>
                                </button>

                                <div x-show="open === {{ $faq->id }}" x-collapse x-cloak>
                                    <div class="prose-rc border-t border-ink-900/6 px-6 py-5 text-[0.92rem]">
                                        {{ $faq->answer }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <x-empty-state icon="alert" title="No questions published yet"
                    message="FAQs added from Admin → FAQs will appear here."/>
            @endforelse

            <div class="card mt-14 flex flex-col items-center gap-5 p-8 text-center sm:flex-row sm:text-left" data-reveal>
                <span class="grid h-14 w-14 flex-none place-items-center rounded-2xl bg-brass-500 text-ink-950">
                    <x-icon name="phone" class="h-6 w-6"/>
                </span>
                <div class="flex-1">
                    <h2 class="heading-display text-lg text-ink-950">Still need help?</h2>
                    <p class="prose-rc mt-1 text-sm">
                        Call the helpdesk on {{ config('rcmaa.contact.hotline') }} ({{ config('rcmaa.contact.hotline_hours') }})
                        or send us a message.
                    </p>
                </div>
                <a href="{{ route('contact') }}" class="btn btn-ink btn-sm flex-none">Contact Us</a>
            </div>
        </div>
    </section>
</x-layout>
