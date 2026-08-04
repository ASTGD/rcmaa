@php $r = $registration; @endphp

<x-layout :title="$title">
    <section class="bg-parchment-dim py-10 md:py-16 print:bg-white print:py-0">
        <div class="container-narrow">

            {{-- Screen-only controls --}}
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4 print:hidden">
                <a href="{{ route('portal.show') }}" class="btn btn-outline btn-sm">
                    <x-icon name="chevron-left" class="h-3.5 w-3.5"/>Back
                </a>
                <button type="button" onclick="window.print()" class="btn btn-ink btn-sm">
                    <x-icon name="download" class="h-3.5 w-3.5"/>Print / save as PDF
                </button>
            </div>

            {{-- The pass --}}
            <article class="overflow-hidden rounded-3xl border border-ink-900/10 bg-white print:rounded-none print:border-0">
                {{-- Header --}}
                <div class="relative overflow-hidden bg-ink-950 px-8 py-7 print:bg-ink-950">
                    <div class="bg-grid-light pointer-events-none absolute inset-0 print:hidden"></div>
                    <div class="relative flex items-center justify-between gap-6">
                        <div class="flex items-center gap-3.5">
                            <img src="{{ asset('media/logo.png') }}" alt="" class="h-12 w-12 flex-none object-contain">
                            <div>
                                <p class="heading-display text-lg leading-tight text-parchment">RCMAA</p>
                                <p class="font-mono text-[0.55rem] uppercase tracking-[0.2em] text-brass-500">
                                    Rajshahi College &middot; Mathematics
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-mono text-[0.58rem] uppercase tracking-[0.18em] text-brass-400">Entry pass</p>
                            <p class="mt-1 text-sm font-semibold text-parchment">Math Nexus 2026</p>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="grid gap-8 p-8 sm:grid-cols-[7rem_1fr]">
                    <div>
                        <div class="aspect-4/5 w-full overflow-hidden rounded-xl bg-ink-900">
                            @if ($r->photo_url)
                                <img src="{{ $r->photo_url }}" alt="{{ $r->full_name_en }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full place-items-center">
                                    <span class="heading-display text-3xl text-brass-500/70">
                                        {{ mb_strtoupper(mb_substr($r->full_name_en, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="min-w-0">
                        <p class="font-mono text-[0.58rem] uppercase tracking-[0.18em] text-brass-700">Reference</p>
                        <p class="heading-display text-3xl tracking-wider text-ink-950">{{ $r->reference }}</p>

                        <h1 class="heading-display mt-5 text-2xl text-ink-950">{{ $r->full_name_en }}</h1>
                        @if ($r->full_name_bn)
                            <p lang="bn" class="font-bangla text-ink-500">{{ $r->full_name_bn }}</p>
                        @endif

                        <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-3.5 text-sm">
                            @foreach ([
                                'Category' => $r->category_label,
                                'Session' => $r->session,
                                'T-shirt' => $r->tshirt_size,
                                'Guests' => $r->guest_total,
                                'Blood group' => $r->blood_group ?: '—',
                                'Cultural programme' => $r->cultural_program ? 'Performing' : 'No',
                            ] as $k => $v)
                                <div>
                                    <dt class="font-mono text-[0.55rem] uppercase tracking-[0.16em] text-ink-400">{{ $k }}</dt>
                                    <dd class="mt-0.5 font-medium text-ink-900">{{ $v }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>

                {{-- Guests --}}
                @if ($r->guests)
                    <div class="border-t border-ink-900/8 px-8 py-5">
                        <p class="font-mono text-[0.55rem] uppercase tracking-[0.16em] text-ink-400">
                            Accompanying guests
                        </p>
                        <p class="mt-1.5 text-sm text-ink-800">
                            {{ collect($r->guests)->pluck('name')->filter()->implode(' &middot; ') }}
                        </p>
                    </div>
                @endif

                {{-- Footer --}}
                <div class="flex flex-wrap items-center justify-between gap-4 border-t border-ink-900/8 bg-parchment px-8 py-5 print:bg-white">
                    <div class="text-[0.72rem] leading-relaxed text-ink-500">
                        <p><strong class="text-ink-800">{{ \Carbon\Carbon::parse(config('rcmaa.registration.event_date'))->format('l, j F Y') }}</strong></p>
                        <p>Rajshahi College Campus, Rajshahi</p>
                    </div>
                    <div class="text-right text-[0.72rem] leading-relaxed text-ink-500">
                        <p>Helpdesk {{ config('rcmaa.contact.hotline') }}</p>
                        <p>{{ config('rcmaa.contact.email') }}</p>
                    </div>
                </div>

                {{-- Status stripe --}}
                @if ($r->payment_status !== 'verified')
                    <div class="bg-amber-100 px-8 py-3 text-center text-[0.78rem] font-semibold text-amber-900">
                        Payment not yet verified — please settle this with the committee before the event day.
                    </div>
                @endif
            </article>

            <p class="mt-6 text-center text-xs text-ink-400 print:hidden">
                Bring this pass, printed or on your phone, to the registration desk.
            </p>
        </div>
    </section>

    @push('head')
        <style>
            @media print {
                header, footer, .print\:hidden { display: none !important; }
                @page { margin: 12mm; }
                body { background: #fff !important; }
            }
        </style>
    @endpush
</x-layout>
