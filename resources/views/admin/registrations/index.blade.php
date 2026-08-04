<x-admin-layout :title="$title">
    <x-slot:actions>
        <a href="{{ route('admin.registrations.export', request()->query()) }}" class="btn btn-ink btn-sm">
            <x-icon name="download" class="h-3.5 w-3.5"/>Export CSV
        </a>
    </x-slot:actions>

    {{-- Status tabs --}}
    <nav class="flex flex-wrap gap-2" aria-label="Filter by status">
        @foreach ([
            '' => ['All', $counts['all']],
            'pending' => ['Pending', $counts['pending']],
            'verified' => ['Verified', $counts['verified']],
            'rejected' => ['Rejected', $counts['rejected']],
        ] as $value => [$label, $count])
            @php $active = ($filters['status'] ?? '') === $value; @endphp
            <a href="{{ route('admin.registrations.index', array_filter([...request()->except('page', 'status'), 'status' => $value])) }}"
               @class([
                   'rounded-full px-4 py-2 text-[0.78rem] font-medium transition-all',
                   'bg-ink-900 text-parchment' => $active,
                   'bg-white text-ink-600 ring-1 ring-ink-900/8 hover:bg-brass-100' => ! $active,
               ])>
                {{ $label }}
                <span class="ml-1.5 opacity-60">{{ $count }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Per-category totals — what the treasurer reconciles against. --}}
    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($categories as $key => $cat)
            @php $row = $byCategory[$key] ?? null; @endphp
            <a href="{{ route('admin.registrations.index', array_filter([...request()->except('page', 'category'), 'category' => $key])) }}"
               @class([
                   'card p-4 transition',
                   'ring-2 ring-brass-500' => ($filters['category'] ?? null) === $key,
                   'hover:bg-brass-100/40' => ($filters['category'] ?? null) !== $key,
               ])>
                <p lang="bn" class="font-bangla text-[0.78rem] text-ink-500">{{ $cat['label_bn'] }}</p>
                <p class="mt-0.5 text-[0.72rem] text-ink-400">{{ $cat['label'] }} &middot; &#2547;{{ number_format($cat['fee']) }}</p>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="heading-display text-2xl text-ink-950">{{ $row->total ?? 0 }}</span>
                    <span class="text-[0.72rem] text-ink-500">&#2547;{{ number_format($row->paid ?? 0) }}</span>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.registrations.index') }}" class="card mt-4 p-4">
        <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
        <input type="hidden" name="category" value="{{ $filters['category'] ?? '' }}">
        <div class="grid gap-3 md:grid-cols-[1fr_9rem_11rem_8rem_auto] md:items-end">
            <div>
                <label for="q" class="field-label !text-xs">Search</label>
                <input id="q" name="q" type="search" class="input !py-2.5 !text-sm"
                       placeholder="Name, email, mobile, reference or TrxID"
                       value="{{ $filters['q'] ?? '' }}">
            </div>
            <div>
                <label for="year" class="field-label !text-xs">Year</label>
                <select id="year" name="year" class="input !py-2.5 !text-sm">
                    <option value="">All</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected(($filters['year'] ?? null) == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="degree" class="field-label !text-xs">Degree</label>
                <select id="degree" name="degree" class="input !py-2.5 !text-sm">
                    <option value="">All</option>
                    @foreach (config('rcmaa.options.degrees') as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['degree'] ?? null) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tshirt" class="field-label !text-xs">T-shirt</label>
                <select id="tshirt" name="tshirt" class="input !py-2.5 !text-sm">
                    <option value="">All</option>
                    @foreach (config('rcmaa.options.tshirt_sizes') as $size)
                        <option value="{{ $size }}" @selected(($filters['tshirt'] ?? null) === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-ink btn-sm h-[2.6rem]">Filter</button>
                @if (array_filter($filters))
                    <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline btn-sm h-[2.6rem]">Clear</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="card mt-4 overflow-hidden">
        @if ($registrations->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[56rem] text-sm">
                    <thead>
                        <tr class="border-b border-ink-900/8 bg-parchment-dim text-left">
                            @foreach (['Registrant', 'Reference', 'Category', 'Session', 'Guests', 'Paid', 'Status', 'Submitted', ''] as $heading)
                                <th class="px-4 py-3 font-mono text-[0.6rem] uppercase tracking-[0.14em] text-ink-500">
                                    {{ $heading }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-900/6">
                        @foreach ($registrations as $registration)
                            <tr class="transition hover:bg-brass-100/40">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-8 w-8 flex-none place-items-center overflow-hidden rounded-lg bg-ink-900 text-[0.7rem] font-semibold text-brass-500">
                                            @if ($registration->photo_url)
                                                <img src="{{ $registration->photo_url }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                {{ mb_strtoupper(mb_substr($registration->full_name_en, 0, 1)) }}
                                            @endif
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-ink-900">{{ $registration->full_name_en }}</p>
                                            <p class="truncate text-xs text-ink-400">{{ $registration->mobile }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-ink-600">{{ $registration->reference }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-[0.78rem] text-ink-700">{{ $registration->category_label }}</span>
                                    <span lang="bn" class="block font-bangla text-[0.72rem] text-ink-400">{{ $registration->category_label_bn }}</span>
                                </td>
                                <td class="px-4 py-3 text-ink-600">
                                    {{ $registration->session }}
                                    <span class="block text-xs text-ink-400">{{ $registration->degree_label }}</span>
                                </td>
                                <td class="px-4 py-3 text-ink-600">{{ $registration->guest_total }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-ink-900">{{ number_format($registration->amount_paid) }}</span>
                                    @if ($registration->balance !== 0)
                                        <span @class([
                                            'block text-xs',
                                            'text-red-600' => $registration->balance < 0,
                                            'text-emerald-600' => $registration->balance > 0,
                                        ])>
                                            {{ $registration->balance > 0 ? '+' : '' }}{{ number_format($registration->balance) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3"><x-admin.status-chip :status="$registration->payment_status"/></td>
                                <td class="px-4 py-3 text-xs text-ink-400">{{ $registration->created_at->format('j M Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.registrations.show', $registration) }}"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-brass-700 transition hover:text-ink-950">
                                        Open<x-icon name="arrow-right" class="h-3.5 w-3.5"/>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-ink-900/8 px-4 py-3">{{ $registrations->links() }}</div>
        @else
            <x-empty-state class="!border-0" icon="search" title="No registrations match"
                message="Try widening the filters, or clear them to see everything."/>
        @endif
    </div>
</x-admin-layout>
