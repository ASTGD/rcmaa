<x-admin-layout :title="$title">
    <nav class="flex gap-2" aria-label="Filter donations">
        @foreach (['' => 'All', 'unverified' => 'Unverified ('.$unverified.')'] as $value => $label)
            @php $active = $filter === $value; @endphp
            <a href="{{ route('admin.donations.index', array_filter(['filter' => $value])) }}"
               @class([
                   'rounded-full px-4 py-2 text-[0.78rem] font-medium transition-all',
                   'bg-ink-900 text-parchment' => $active,
                   'bg-white text-ink-600 ring-1 ring-ink-900/8 hover:bg-brass-100' => ! $active,
               ])>{{ $label }}</a>
        @endforeach
    </nav>

    @if ($donations->isNotEmpty())
        <div class="mt-4 space-y-3" x-data="{ open: null }">
            @foreach ($donations as $donation)
                <div @class(['card overflow-hidden', 'ring-1 ring-brass-500/50' => ! $donation->is_verified])>
                    <button type="button" @click="open = open === {{ $donation->id }} ? null : {{ $donation->id }}"
                            class="flex w-full items-center gap-4 px-5 py-4 text-left"
                            :aria-expanded="open === {{ $donation->id }}">
                        <span @class([
                            'h-2 w-2 flex-none rounded-full',
                            'bg-brass-500' => ! $donation->is_verified,
                            'bg-ink-900/15' => $donation->is_verified,
                        ])></span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink-900">
                                BDT {{ number_format($donation->amount, 2) }} from {{ $donation->donor_name }}
                            </p>
                            <p class="truncate text-xs text-ink-400">
                                Phone: {{ $donation->phone_number }} &middot; TrxID: {{ $donation->transaction_id }}
                            </p>
                        </div>

                        <span @class([
                            'rounded-full px-2 py-0.5 text-[0.62rem] font-bold uppercase tracking-wider',
                            'bg-emerald-100 text-emerald-800' => $donation->is_verified,
                            'bg-amber-100 text-amber-800' => ! $donation->is_verified,
                        ])>
                            {{ $donation->is_verified ? 'Verified' : 'Pending' }}
                        </span>

                        <time class="flex-none text-xs text-ink-400">{{ $donation->created_at->diffForHumans() }}</time>
                        <x-icon name="chevron-down" class="h-4 w-4 flex-none text-ink-300 transition-transform"
                                ::class="open === {{ $donation->id }} && 'rotate-180'"/>
                    </button>

                    <div x-show="open === {{ $donation->id }}" x-collapse x-cloak>
                        <div class="border-t border-ink-900/6 px-5 py-4 bg-parchment-dim/10">
                            <div class="grid gap-3 text-sm max-w-xl">
                                <div class="flex justify-between border-b border-ink-900/5 pb-2">
                                    <span class="text-ink-500">Donor Name:</span>
                                    <span class="font-semibold text-ink-900">{{ $donation->donor_name }}</span>
                                </div>
                                <div class="flex justify-between border-b border-ink-900/5 pb-2">
                                    <span class="text-ink-500">Phone Number:</span>
                                    <span class="font-semibold text-ink-900">{{ $donation->phone_number }}</span>
                                </div>
                                <div class="flex justify-between border-b border-ink-900/5 pb-2">
                                    <span class="text-ink-500">Donation Amount:</span>
                                    <span class="font-semibold text-ink-900">BDT {{ number_format($donation->amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between border-b border-ink-900/5 pb-2">
                                    <span class="text-ink-500">Transaction ID / Ref:</span>
                                    <span class="font-semibold text-ink-900">{{ $donation->transaction_id }}</span>
                                </div>
                                <div class="flex justify-between border-b border-ink-900/5 pb-2">
                                    <span class="text-ink-500">Submitted At:</span>
                                    <span class="font-semibold text-ink-900">{{ $donation->created_at->format('j M Y, h:i A') }}</span>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2">
                                @if ($donation->receipt_path)
                                    <a href="{{ Storage::disk('public')->url($donation->receipt_path) }}"
                                       target="_blank"
                                       class="btn btn-ink btn-sm">
                                        <x-icon name="image" class="h-3.5 w-3.5"/>View Receipt / Screenshot
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('admin.donations.update', $donation) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="is_verified" value="{{ $donation->is_verified ? 0 : 1 }}">
                                    <button type="submit" @class([
                                        'btn btn-sm',
                                        'btn-outline' => $donation->is_verified,
                                        'btn-primary' => ! $donation->is_verified,
                                    ])>
                                        {{ $donation->is_verified ? 'Mark as Unverified' : 'Verify Donation' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.donations.destroy', $donation) }}"
                                      onsubmit="return confirm('Delete this donation record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="rounded-full border border-red-200 px-4 py-2 text-[0.68rem] font-semibold uppercase tracking-[0.08em] text-red-700 transition hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $donations->links() }}</div>
    @else
        <x-empty-state class="mt-4" icon="heart" title="No donations"
            message="Donation payments submitted by donors will show up here."/>
    @endif
</x-admin-layout>
