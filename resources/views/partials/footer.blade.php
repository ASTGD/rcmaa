<footer data-theme="dark" class="relative overflow-hidden bg-ink-950 text-ink-200">
    <div class="bg-grid-light pointer-events-none absolute inset-0"></div>

    {{-- Oversized wordmark bleeding off the baseline. --}}
    <div class="pointer-events-none absolute inset-x-0 -bottom-6 select-none overflow-hidden">
        <p class="heading-display whitespace-nowrap text-center text-[18vw] leading-none text-white/[0.035]">
            RCMAA
        </p>
    </div>

    <div class="container-rc relative">
        {{-- Closing statement --}}
        <div class="grid gap-10 border-b border-white/8 py-16 lg:grid-cols-[1.4fr_1fr] lg:items-end lg:py-20">
            <div data-reveal="split">
                <p class="heading-display max-w-2xl text-[clamp(1.6rem,3.2vw,2.6rem)] text-parchment">
                    The RCMAA serves as a bridge connecting our proud past with a brilliant future.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 lg:justify-end" data-reveal data-reveal-delay="0.15">
                @auth('alumni')
                    <a href="{{ route('member.dashboard') }}" class="btn btn-primary">My Account / Dashboard</a>
                @else
                    <a href="{{ route('register.create') }}" class="btn btn-primary">Register for the Reunion</a>
                @endauth
                <a href="{{ route('contact') }}" class="btn btn-outline-light">Contact Us</a>
            </div>
        </div>

        {{-- Columns --}}
        <div class="grid gap-12 py-14 md:grid-cols-2 lg:grid-cols-[1.6fr_1fr_1fr_1.2fr]">
            <div>
                <x-logo light class="mb-5" />
                <p class="max-w-sm text-sm leading-relaxed text-ink-300">
                    {{ config('rcmaa.tagline') }} Honoring our heritage, fostering our future.
                </p>
                @include('partials.social', ['class' => 'mt-6 gap-4 text-ink-300', 'size' => 'h-6 w-6'])
            </div>

            @foreach ([
                    'Explore' => [
                        ['Home', 'home'],
                        ['Events', 'events.index'],
                        ['Gallery', 'gallery'],
                        ['Notice', 'notices.index'],
                        ['Directory', 'directory'],
                    ],
                    'Association' => [
                        ['About RCMAA', 'about'],
                        ['Our Goal', 'our-goal'],
                        ['Committee', 'committee'],
                        ['Faculty', 'teachers'],
                        ['How to Apply', 'how-to-apply'],
                    ],
                ] as $heading => $links)
                <nav aria-label="{{ $heading }}">
                    <h2 class="font-mono text-[0.66rem] uppercase tracking-[0.22em] text-brass-500">{{ $heading }}</h2>
                    <ul class="mt-5 space-y-3">
                        @foreach ($links as [$label, $route])
                            <li>
                                <a href="{{ route($route) }}"
                                    class="group inline-flex items-center gap-1.5 text-sm text-ink-300 transition-colors hover:text-parchment">
                                    <span class="h-px w-0 bg-brass-500 transition-all duration-300 group-hover:w-3"></span>
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach

            <div>
                <h2 class="font-mono text-[0.66rem] uppercase tracking-[0.22em] text-brass-500">Get in Touch</h2>
                <ul class="mt-5 space-y-4 text-sm text-ink-300">
                    <li class="flex gap-3">
                        <x-icon name="map-pin" class="mt-0.5 h-4 w-4 flex-none text-brass-500" />
                        <span>{{ config('rcmaa.contact.address') }}</span>
                    </li>
                    <li class="flex gap-3">
                        <x-icon name="mail" class="mt-0.5 h-4 w-4 flex-none text-brass-500" />
                        <a href="mailto:{{ config('rcmaa.contact.email') }}" class="transition hover:text-parchment">
                            {{ config('rcmaa.contact.email') }}
                        </a>
                    </li>
                    <li class="flex gap-3">
                        <x-icon name="phone" class="mt-0.5 h-4 w-4 flex-none text-brass-500" />
                        <a href="tel:{{ config('rcmaa.contact.phone') }}"
                            class="transition hover:text-parchment">{{ config('rcmaa.contact.phone') }}</a>
                    </li>
                    <li class="flex gap-3">
                        <x-icon name="clock" class="mt-0.5 h-4 w-4 flex-none text-brass-500" />
                        <span>Helpdesk {{ config('rcmaa.contact.hotline_hours') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Legal strip --}}
        <div
            class="flex flex-col gap-4 border-t border-white/8 py-7 text-xs text-ink-400 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ config('rcmaa.name') }}. All rights reserved.</p>
            <ul class="flex flex-wrap items-center gap-x-6 gap-y-2">
                @foreach ([
                        ['Contact Us', 'contact'],
                        ['Features', 'features'],
                        ['Privacy Policy', 'privacy'],
                        ['Terms of Service', 'terms'],
                        ['Help Center', 'help-center'],
                    ] as [$label, $route])
                    <li><a href="{{ route($route) }}" class="transition hover:text-brass-400">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>

        {{-- Developer strip --}}
        <div class="border-t border-white/5 py-5 text-center text-xs tracking-wide text-ink-400">
            <span>Designed & Developed with excellence by </span>
            <a href="https://astgd.com/" target="_blank" rel="noopener noreferrer"
                class="font-semibold text-brass-500 transition-colors duration-300 hover:text-parchment">ASTGD</a>
        </div>
    </div>
</footer>