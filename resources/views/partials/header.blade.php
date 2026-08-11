@php
    $committees = \App\Models\CommitteeMember::COMMITTEES;
@endphp

{{-- Utility bar: contact details, matching the old site's top strip. --}}
<div class="hidden bg-ink-900 text-ink-200 lg:block">
    <div class="container-rc flex h-10 items-center justify-between text-[0.72rem]">
        <div class="flex items-center gap-6">
            <a href="mailto:{{ config('rcmaa.contact.email') }}"
               class="flex items-center gap-2 transition hover:text-brass-400">
                <x-icon name="mail" class="h-3.5 w-3.5"/>
                {{ config('rcmaa.contact.email') }}
            </a>
            <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.contact.phone')) }}"
               class="flex items-center gap-2 transition hover:text-brass-400">
                <x-icon name="phone" class="h-3.5 w-3.5"/>
                {{ config('rcmaa.contact.phone') }}
            </a>
        </div>
        <div class="flex items-center gap-5">
            <span class="font-mono uppercase tracking-[0.18em] text-brass-500">
                Grand Reunion &middot; 19 Dec 2026
            </span>
            <span class="h-3 w-px bg-white/15"></span>
            @include('partials.social', ['class' => 'text-ink-300'])
        </div>
    </div>
</div>

<header
    data-header
    x-data="{ open: false, submenu: null }"
    @keydown.escape.window="open = false; submenu = null"
    class="sticky top-0 z-50 border-b border-ink-900/8 bg-parchment/85 backdrop-blur-xl transition-[transform,background-color,border-color] duration-500
           [&.is-stuck]:shadow-[0_10px_40px_-24px_rgba(7,14,27,.4)]
           [&.is-over-dark]:border-white/10 [&.is-over-dark]:bg-ink-900/80">
    <nav class="container-rc flex h-[4.6rem] items-center justify-between gap-6" aria-label="Primary">
        <a href="{{ route('home') }}" class="group flex-none" aria-label="{{ config('rcmaa.name') }} — home">
            <x-logo size="h-10 w-10" class="transition-transform duration-500 group-hover:rotate-[-8deg]"/>
        </a>

        {{-- Desktop nav --}}
        <ul class="hidden items-center gap-1 xl:flex">
            @foreach ([
                ['label' => 'Home', 'bn' => 'হোম', 'route' => 'home'],
                ['label' => 'About', 'bn' => 'আমাদের সম্পর্কে', 'route' => 'about', 'children' => [
                    ['label' => 'Our Heritage', 'bn' => 'আমাদের ঐতিহ্য', 'route' => 'heritage'],
                    ['label' => 'History', 'bn' => 'ইতিহাস', 'route' => 'about'],
                    ['label' => 'Our Journey', 'bn' => 'আমাদের পথচলা', 'route' => 'our-goal', 'fragment' => 'journey'],
                    ['label' => 'Aims', 'bn' => 'লক্ষ্য', 'route' => 'our-goal', 'fragment' => 'aims'],
                    ['label' => 'Objectives', 'bn' => 'উদ্দেশ্য', 'route' => 'our-goal', 'fragment' => 'objectives'],
                ]],
                ['label' => 'Committee', 'bn' => 'কমিটি', 'route' => 'committee', 'children' => 'committees'],
                ['label' => 'Events', 'bn' => 'ইভেন্টসমূহ', 'route' => 'events.index'],
                ['label' => 'Gallery', 'bn' => 'গ্যালারি', 'route' => 'gallery'],
                ['label' => 'Directory', 'bn' => 'অ্যালামনাই ডিরেক্টরি', 'route' => 'directory'],
                ['label' => 'Contact', 'bn' => 'যোগাযোগ', 'route' => 'contact'],
                ['label' => 'FAQ', 'bn' => 'FAQ', 'route' => 'faqs'],
            ] as $item)
                @php
                    $isActive = request()->routeIs($item['route']) ||
                        (isset($item['children']) && $item['label'] === 'Committee' && request()->routeIs('committee'));
                @endphp
                <li class="relative"
                    @isset($item['children']) @mouseenter="submenu = '{{ $item['label'] }}'" @mouseleave="submenu = null" @endisset>
                    <a href="{{ route($item['route']) }}"
                       {{-- Each utility carries its own variant. Writing
                            [.is-over-dark_&]:{{ '...' }} around a two-class string
                            scoped only the first one, so `hover:text-white` escaped
                            and applied on the light header too — hovering a nav item
                            turned it white on near-white and it vanished. --}}
                       @class([
                           'relative flex items-center gap-1.5 rounded-full px-3.5 py-2 text-[0.8rem] font-medium transition-colors duration-300',
                           'header-link is-current text-brass-700' => $isActive,
                           'header-link text-ink-700 hover:text-ink-950' => ! $isActive,
                       ])>
                        {{ $item['label'] }}
                        @isset($item['children'])
                            <x-icon name="chevron-down" class="h-3 w-3 opacity-50"/>
                        @endisset
                        @if ($isActive)
                            <span class="absolute inset-x-3.5 -bottom-0.5 h-px bg-brass-600"></span>
                        @endif
                    </a>

                    @isset($item['children'])
                        <div x-show="submenu === '{{ $item['label'] }}'" x-cloak
                             x-transition:enter="transition ease-out duration-250"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="absolute left-0 top-full z-50 w-72 pt-3">
                            <div class="overflow-hidden rounded-2xl border border-ink-900/8 bg-white p-2 shadow-[0_28px_70px_-30px_rgba(7,14,27,.45)]">
                                @if ($item['children'] === 'committees')
                                    @foreach ($committees as $key => $labels)
                                        <a href="{{ route('committee', ['group' => $key]) }}"
                                           class="group flex flex-col gap-0.5 rounded-xl px-3.5 py-2.5 transition hover:bg-brass-100">
                                            <span class="text-[0.82rem] font-medium text-ink-800">{{ $labels['en'] }}</span>
                                            <span lang="bn" class="text-[0.72rem] text-ink-400">{{ $labels['bn'] }}</span>
                                        </a>
                                    @endforeach
                                @else
                                    @foreach ($item['children'] as $child)
                                        <a href="{{ route($child['route']).(isset($child['fragment']) ? '#'.$child['fragment'] : '') }}"
                                           class="group flex items-center justify-between gap-3 rounded-xl px-3.5 py-2.5 transition hover:bg-brass-100">
                                            <span class="flex flex-col">
                                                <span class="text-[0.82rem] font-medium text-ink-800">{{ $child['label'] }}</span>
                                                <span lang="bn" class="text-[0.72rem] text-ink-400">{{ $child['bn'] }}</span>
                                            </span>
                                            <x-icon name="arrow-up-right" class="h-3.5 w-3.5 flex-none text-brass-600"/>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endisset
                </li>
            @endforeach
        </ul>

        <div class="flex flex-none items-center gap-3">
            <a href="{{ auth('alumni')->check() ? route('member.dashboard') : route('member.login') }}"
               class="header-link hidden text-[0.78rem] font-semibold uppercase tracking-[0.12em] text-ink-600 transition hover:text-ink-950 lg:block ">
                {{ auth('alumni')->check() ? 'My account' : 'Login' }}
            </a>
            @if (auth('alumni')->check())
                <form method="POST" action="{{ route('member.logout') }}" class="inline-flex">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm hidden sm:inline-flex">
                        Sign Out
                    </button>
                </form>
            @else
                <a href="{{ route('register.create') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">
                    Register Now
                </a>
            @endif

            <button type="button" @click="open = !open"
                    class="relative grid h-11 w-11 place-items-center rounded-full border border-ink-900/12 transition hover:border-ink-900/30 xl:hidden [.is-over-dark_&]:border-white/20"
                    :aria-expanded="open" aria-controls="mobile-nav" aria-label="Toggle navigation">
                <span class="sr-only">Menu</span>
                <span class="flex h-3 w-4 flex-col justify-between">
                    <span class="h-px w-full bg-current transition-transform duration-300 [.is-over-dark_&]:bg-parchment"
                          :class="open && 'translate-y-[5.5px] rotate-45'"></span>
                    <span class="h-px w-full bg-current transition-opacity duration-200 [.is-over-dark_&]:bg-parchment"
                          :class="open && 'opacity-0'"></span>
                    <span class="h-px w-full bg-current transition-transform duration-300 [.is-over-dark_&]:bg-parchment"
                          :class="open && '-translate-y-[5.5px] -rotate-45'"></span>
                </span>
            </button>
        </div>
    </nav>

    {{-- Mobile / tablet drawer --}}
    <div id="mobile-nav" x-show="open" x-cloak x-collapse
         class="border-t border-ink-900/8 bg-parchment xl:hidden">
        <div class="container-rc max-h-[75vh] overflow-y-auto py-6">
            <ul class="flex flex-col gap-1">
                @foreach ([
                    ['label' => 'Home', 'bn' => 'হোম', 'route' => 'home'],
                    ['label' => 'Our Heritage', 'bn' => 'আমাদের ঐতিহ্য', 'route' => 'heritage'],
                    ['label' => 'History', 'bn' => 'ইতিহাস', 'route' => 'about'],
                    ['label' => 'Our Goal', 'bn' => 'লক্ষ্য ও উদ্দেশ্য', 'route' => 'our-goal'],
                    ['label' => 'Events', 'bn' => 'ইভেন্টসমূহ', 'route' => 'events.index'],
                    ['label' => 'Gallery', 'bn' => 'গ্যালারি', 'route' => 'gallery'],
                    ['label' => 'Directory', 'bn' => 'অ্যালামনাই ডিরেক্টরি', 'route' => 'directory'],
                    ['label' => 'Contact', 'bn' => 'যোগাযোগ', 'route' => 'contact'],
                    ['label' => 'FAQ', 'bn' => 'FAQ', 'route' => 'faqs'],
                ] as $item)
                    <li>
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center justify-between border-b border-ink-900/6 py-3.5">
                            <span class="flex flex-col">
                                <span class="text-[0.95rem] font-medium text-ink-800">{{ $item['label'] }}</span>
                                <span lang="bn" class="text-[0.75rem] text-ink-400">{{ $item['bn'] }}</span>
                            </span>
                            <x-icon name="arrow-up-right" class="h-4 w-4 flex-none text-brass-600"/>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-5">
                <p class="eyebrow mb-3">Committees</p>
                <div class="grid gap-1.5 sm:grid-cols-2">
                    @foreach ($committees as $key => $labels)
                        <a href="{{ route('committee', ['group' => $key]) }}"
                           class="rounded-xl bg-white px-3.5 py-2.5 text-[0.8rem] font-medium text-ink-700 ring-1 ring-ink-900/6">
                            {{ $labels['en'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3">
                @auth('alumni')
                    <a href="{{ route('member.dashboard') }}" class="btn btn-primary w-full">My account</a>
                @else
                    <a href="{{ route('register.create') }}" class="btn btn-primary w-full">Register for the Reunion</a>
                    <a href="{{ route('member.login') }}" class="btn btn-outline w-full">Member login</a>
                @endauth
            </div>
        </div>
    </div>
</header>
