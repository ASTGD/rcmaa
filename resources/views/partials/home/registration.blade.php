@php
    $categories = config('rcmaa.registration.categories');
    $guestFee = config('rcmaa.registration.guest_fee');
    $cheapest = collect($categories)->min('fee');
    $dearest = collect($categories)->max('fee');
    $eventDate = \Carbon\Carbon::parse(config('rcmaa.registration.event_date'));
@endphp

<section data-theme="dark" class="relative overflow-hidden bg-ink-950 py-24 md:py-32" data-parallax-scope>
    {{-- Oversized sigma watermark --}}
    <div class="pointer-events-none absolute -right-20 top-1/2 -translate-y-1/2 select-none opacity-[0.045]" data-parallax="0.12">
        <x-icon name="sigma" class="h-[36rem] w-[36rem] text-parchment"/>
    </div>
    <div class="bg-grid-light pointer-events-none absolute inset-0"></div>

    <div class="container-rc relative">
        <div class="grid gap-14 lg:grid-cols-[1.15fr_1fr] lg:items-center lg:gap-20">
            <div>
                <p class="eyebrow eyebrow-light" data-reveal>
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brass-500 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-brass-500"></span>
                    </span>
                    Registration going on
                </p>

                <h2 class="heading-display mt-6 text-[clamp(2rem,4.4vw,3.4rem)] text-parchment" data-reveal="split">
                    Secure your seat at the Grand Reunion 2026
                </h2>

                <p class="prose-rc mt-6 max-w-xl !text-ink-200" data-reveal data-reveal-delay="0.15">
                    Don't miss the chance to reunite with old friends, network with fellow mathematics
                    graduates, and celebrate our shared legacy. Secure your spot today and be part of
                    this memorable milestone event at Rajshahi College.
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-4" data-reveal data-reveal-delay="0.25">
                    @auth('alumni')
                        <a href="{{ route('member.dashboard') }}" class="btn btn-primary">
                            My Account
                            <x-icon name="arrow-right" class="h-4 w-4"/>
                        </a>
                    @else
                        <a href="{{ route('register.create') }}" class="btn btn-primary">
                            Register Now
                            <x-icon name="arrow-right" class="h-4 w-4"/>
                        </a>
                    @endauth
                    <a href="{{ route('help-center') }}" class="btn btn-outline-light">Need Help?</a>
                </div>

                <p class="mt-6 text-sm text-ink-400" data-reveal data-reveal-delay="0.3">
                    Already registered?
                    <a href="{{ route('registration.status') }}" class="text-brass-400 underline underline-offset-4 transition hover:text-brass-300">
                        Check your status
                    </a>
                </p>
            </div>

            {{-- Fee & logistics card --}}
            <div class="relative rounded-3xl border border-white/10 bg-ink-900/70 p-8 backdrop-blur-sm md:p-10"
                 data-reveal="scale" data-reveal-delay="0.2">
                <div class="absolute -top-px left-10 right-10 h-px bg-gradient-to-r from-transparent via-brass-500 to-transparent"></div>

                <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-400">
                    {{ config('rcmaa.registration.event_name') }}
                </p>

                <div class="mt-6 flex items-baseline gap-2">
                    <span class="text-lg text-ink-300">BDT</span>
                    <span class="heading-display text-6xl text-parchment" data-count="{{ $cheapest }}">0</span>
                    <span class="text-lg text-ink-300">&ndash;&nbsp;{{ number_format($dearest) }}</span>
                </div>
                <p class="mt-2 text-sm text-ink-400">depending on your category &mdash; teachers, alumni, recent graduates and current students each pay a different fee</p>

                <div class="rule-brass my-7"></div>

                <ul class="space-y-4 text-sm text-ink-200">
                    @foreach ([
                        ['calendar', $eventDate->format('l, j F Y')],
                        ['map-pin', 'Rajshahi College Campus, Rajshahi'],
                        ['users', 'Accompanying guest — BDT '.number_format($guestFee).' each (teachers and alumni only)'],
                        ['check-circle', 'Includes reunion kit, T-shirt, lunch and the cultural evening'],
                    ] as [$icon, $line])
                        <li class="flex gap-3">
                            <x-icon :name="$icon" class="mt-0.5 h-4 w-4 flex-none text-brass-500"/>
                            <span>{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-8 rounded-2xl bg-ink-800/70 p-5">
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.12em] text-brass-400">Helpdesk</p>
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.contact.hotline')) }}"
                       class="mt-2 block text-lg font-medium text-parchment transition hover:text-brass-400">
                        {{ config('rcmaa.contact.hotline') }}
                    </a>
                    <p class="mt-1 text-xs text-ink-400">{{ config('rcmaa.contact.hotline_hours') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
