<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Helpdesk"
        title="Help Center"
        lead="Everything you need to complete your registration, plus a direct line to the people who can fix it if something goes wrong."
        :breadcrumbs="['Help Center' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc">
            {{-- Contact channels, as published by the association --}}
            <div class="grid gap-5 md:grid-cols-3" data-reveal data-reveal-stagger="0.1">
                @foreach ([
                    ['phone', 'Registration Helpline', config('rcmaa.contact.hotline'), config('rcmaa.contact.hotline_hours'), 'tel:'.preg_replace('/[^\d+]/', '', config('rcmaa.contact.hotline'))],
                    ['clock', 'Helpdesk', config('rcmaa.contact.helpdesk'), config('rcmaa.contact.helpdesk_hours'), 'tel:'.preg_replace('/[^\d+]/', '', config('rcmaa.contact.helpdesk'))],
                    ['mail', 'Email', config('rcmaa.contact.email'), 'Replies within two working days', 'mailto:'.config('rcmaa.contact.email')],
                ] as [$icon, $label, $value, $note, $href])
                    <a href="{{ $href }}" class="card card-hover group p-7" data-reveal-item>
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-ink-900 text-brass-500 transition-colors duration-500 group-hover:bg-brass-500 group-hover:text-ink-950">
                            <x-icon :name="$icon" class="h-5 w-5"/>
                        </span>
                        <p class="mt-6 font-mono text-[0.62rem] uppercase tracking-[0.18em] text-brass-700">{{ $label }}</p>
                        <p class="mt-2 text-base font-semibold text-ink-950">{{ $value }}</p>
                        <p class="mt-1 text-xs text-ink-400">{{ $note }}</p>
                    </a>
                @endforeach
            </div>

            {{-- How registration works --}}
            <div class="mt-20">
                <x-section-heading eyebrow="Step by step" title="How registration works"/>

                <ol class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-4" data-reveal data-reveal-stagger="0.1">
                    @foreach ([
                        ['Fill the form', 'Six short steps covering your personal, academic and professional details, T-shirt size and any guests. Progress is saved in your browser.'],
                        ['Send the payment', 'Transfer the total shown on the last step to one of the listed bKash, Nagad, Rocket or bank accounts.'],
                        ['Submit the transaction ID', 'Enter the TrxID from your confirmation SMS, the number you sent it from, and the exact amount.'],
                        ['Get verified', 'The committee checks the payment manually — usually one to two working days — and you receive a confirmation email.'],
                    ] as $i => [$heading, $body])
                        <li class="relative" data-reveal-item>
                            <span class="heading-display block text-5xl text-brass-500/45">0{{ $i + 1 }}</span>
                            <h3 class="heading-display mt-3 text-lg text-ink-950">{{ $heading }}</h3>
                            <p class="prose-rc mt-2 text-sm">{{ $body }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Registration/payment FAQs --}}
            @if ($faqs->isNotEmpty())
                <div class="mt-20">
                    <x-section-heading eyebrow="Common questions" title="Registration and payment" size="sm"/>

                    <div class="mt-10 grid gap-4 md:grid-cols-2" x-data="{ open: null }">
                        @foreach ($faqs as $faq)
                            <div class="card overflow-hidden self-start" data-reveal>
                                <button type="button" @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}"
                                        class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                                        :aria-expanded="open === {{ $faq->id }}">
                                    <span class="text-[0.92rem] font-semibold text-ink-900">{{ $faq->question }}</span>
                                    <span class="grid h-7 w-7 flex-none place-items-center rounded-full bg-ink-900/5 transition-transform duration-300"
                                          :class="open === {{ $faq->id }} && 'rotate-45 bg-brass-500'">
                                        <x-icon name="plus" class="h-3.5 w-3.5 text-ink-700"/>
                                    </span>
                                </button>
                                <div x-show="open === {{ $faq->id }}" x-collapse x-cloak>
                                    <p class="prose-rc border-t border-ink-900/6 px-6 py-5 text-[0.88rem]">{{ $faq->answer }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 text-center">
                        <a href="{{ route('faqs') }}" class="btn btn-outline btn-sm">See all FAQs</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-layout>
