<x-admin-layout :title="$title">
    <x-slot:actions>
        <a href="{{ route('admin.registrations.export') }}" class="btn btn-ink btn-sm">
            <x-icon name="download" class="h-3.5 w-3.5"/>Export CSV
        </a>
    </x-slot:actions>

    {{-- Headline numbers --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Total registrations', number_format($stats['total']), 'users', 'All submissions received'],
            ['Awaiting verification', number_format($stats['pending']), 'clock', 'Payments still to be checked'],
            ['Verified', number_format($stats['verified']), 'check-circle', number_format($stats['guests']).' accompanying guests'],
            ['Collected (verified)', 'BDT '.number_format($stats['collected']), 'shield', 'BDT '.number_format($stats['awaiting']).' pending verification'],
        ] as $i => [$label, $value, $icon, $note])
            <div class="card p-6">
                <div class="flex items-start justify-between gap-3">
                    <p class="font-mono text-[0.6rem] uppercase tracking-[0.18em] text-brass-700">{{ $label }}</p>
                    <span class="grid h-9 w-9 flex-none place-items-center rounded-xl bg-brass-100 text-brass-700">
                        <x-icon :name="$icon" class="h-4 w-4"/>
                    </span>
                </div>
                <p class="heading-display mt-4 text-3xl text-ink-950">{{ $value }}</p>
                <p class="mt-1.5 text-xs text-ink-400">{{ $note }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.5fr_1fr]">

        {{-- Recent registrations --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-ink-900/8 px-6 py-4">
                <h2 class="text-sm font-semibold text-ink-900">Recent registrations</h2>
                <a href="{{ route('admin.registrations.index') }}"
                   class="text-xs font-semibold text-brass-700 transition hover:text-ink-950">View all</a>
            </div>

            @if ($recent->isNotEmpty())
                <div class="divide-y divide-ink-900/6">
                    @foreach ($recent as $registration)
                        <a href="{{ route('admin.registrations.show', $registration) }}"
                           class="flex items-center gap-4 px-6 py-3.5 transition hover:bg-brass-100/50">
                            <span class="grid h-9 w-9 flex-none place-items-center overflow-hidden rounded-lg bg-ink-900 text-xs font-semibold text-brass-500">
                                @if ($registration->photo_url)
                                    <img src="{{ $registration->photo_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    {{ mb_strtoupper(mb_substr($registration->full_name_en, 0, 1)) }}
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-ink-900">{{ $registration->full_name_en }}</p>
                                <p class="truncate text-xs text-ink-400">
                                    {{ $registration->reference }} &middot; {{ $registration->session }} &middot;
                                    {{ $registration->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <x-admin.status-chip :status="$registration->payment_status"/>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="px-6 py-10 text-center text-sm text-ink-400">No registrations yet.</p>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Fortnight trend --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink-900">Last 14 days</h2>
                @php $peak = max(1, $trend->max() ?? 1); @endphp

                @if ($trend->isNotEmpty())
                    <div class="mt-6 flex h-28 items-end gap-1.5">
                        @foreach ($trend as $day => $count)
                            <div class="group relative flex-1">
                                <div class="w-full rounded-t bg-brass-500 transition-colors group-hover:bg-ink-900"
                                     style="height: {{ max(4, round($count / $peak * 100)) }}px"></div>
                                <span class="pointer-events-none absolute -top-7 left-1/2 -translate-x-1/2 rounded bg-ink-900 px-1.5 py-0.5 text-[0.6rem] text-parchment opacity-0 transition group-hover:opacity-100">
                                    {{ $count }} on {{ \Carbon\Carbon::parse($day)->format('j M') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-6 text-sm text-ink-400">No registrations in the last fortnight.</p>
                @endif
            </div>

            {{-- T-shirt tally, needed for the merchandise order --}}
            <div class="card p-6">
                <h2 class="text-sm font-semibold text-ink-900">T-shirt sizes</h2>
                <p class="mt-1 text-xs text-ink-400">All registrations, for the merchandise order.</p>

                <div class="mt-5 space-y-2.5">
                    @foreach (config('rcmaa.options.tshirt_sizes') as $size)
                        @php
                            $count = $tshirts[$size] ?? 0;
                            $max = max(1, $tshirts->max() ?? 1);
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-10 flex-none font-mono text-xs text-ink-500">{{ $size }}</span>
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-ink-900/8">
                                <div class="h-full rounded-full bg-brass-500" style="width: {{ round($count / $max * 100) }}%"></div>
                            </div>
                            <span class="w-8 flex-none text-right text-xs font-semibold text-ink-800">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Batches --}}
            @if ($byYear->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-sm font-semibold text-ink-900">Best-represented years</h2>
                    <ul class="mt-4 space-y-2">
                        @foreach ($byYear as $row)
                            <li class="flex items-center justify-between text-sm">
                                <a href="{{ route('admin.registrations.index', ['year' => $row->passing_year]) }}"
                                   class="text-ink-600 transition hover:text-brass-700">{{ $row->passing_year }}</a>
                                <span class="font-semibold text-ink-900">{{ $row->total }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick links --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Add an event', 'calendar', route('admin.content.create', 'events')],
            ['Post a notice', 'bell', route('admin.content.create', 'notices')],
            ['Upload photos', 'camera', route('admin.content.create', 'gallery')],
            ['Add committee member', 'users', route('admin.content.create', 'committee')],
        ] as [$label, $icon, $href])
            <a href="{{ $href }}" class="card card-hover group flex items-center gap-3.5 p-5">
                <span class="grid h-10 w-10 flex-none place-items-center rounded-xl bg-ink-900 text-brass-500 transition-colors duration-500 group-hover:bg-brass-500 group-hover:text-ink-950">
                    <x-icon :name="$icon" class="h-4 w-4"/>
                </span>
                <span class="text-sm font-medium text-ink-900">{{ $label }}</span>
                <x-icon name="arrow-up-right" class="ml-auto h-4 w-4 text-ink-300"/>
            </a>
        @endforeach
    </div>
</x-admin-layout>
