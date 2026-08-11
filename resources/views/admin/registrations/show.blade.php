<x-admin-layout :title="'Registration ' . $registration->reference">
    <x-slot:actions>
        <a href="{{ route('admin.registrations.edit', $registration) }}" class="btn btn-ink btn-sm">
            <x-icon name="book" class="h-3.5 w-3.5" />Edit details
        </a>
        <a href="{{ route('admin.registrations.index') }}" class="btn btn-outline btn-sm">
            <x-icon name="chevron-left" class="h-3.5 w-3.5" />Back
        </a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-[1fr_22rem] lg:items-start">

        {{-- Record --}}
        <div class="space-y-6">
            {{-- Identity --}}
            <div class="card p-6">
                <div class="flex flex-wrap items-start gap-5">
                    <span
                        class="grid h-24 w-20 flex-none place-items-center overflow-hidden rounded-xl bg-ink-900 text-2xl font-semibold text-brass-500">
                        @if ($registration->photo_url)
                            <img src="{{ $registration->photo_url }}" alt="{{ $registration->full_name_en }}"
                                class="h-full w-full object-cover">
                        @else
                            {{ mb_strtoupper(mb_substr($registration->full_name_en, 0, 1)) }}
                        @endif
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="heading-display text-2xl text-ink-950">{{ $registration->full_name_en }}</h2>
                            <x-admin.status-chip :status="$registration->payment_status" />
                        </div>
                        @if ($registration->full_name_bn)
                            <p lang="bn" class="mt-1 text-ink-500">{{ $registration->full_name_bn }}</p>
                        @endif
                        <p class="mt-2 font-mono text-sm text-brass-700">{{ $registration->reference }}</p>

                        <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                            <a href="tel:{{ $registration->mobile }}"
                                class="flex items-center gap-2 text-ink-600 transition hover:text-brass-700">
                                <x-icon name="phone" class="h-4 w-4 text-brass-600" />{{ $registration->mobile }}
                            </a>
                            <a href="mailto:{{ $registration->email }}"
                                class="flex items-center gap-2 text-ink-600 transition hover:text-brass-700">
                                <x-icon name="mail" class="h-4 w-4 text-brass-600" />{{ $registration->email }}
                            </a>
                            @if ($registration->whatsapp)
                                <span class="flex items-center gap-2 text-ink-600">
                                    <x-icon name="whatsapp" class="h-4 w-4 text-brass-600" />{{ $registration->whatsapp }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail sections --}}
            @foreach ([
                    'Registration' => [
                        'Category' => $registration->category_label
                            . ($registration->category_label_bn ? ' · ' . $registration->category_label_bn : ''),
                        'Category fee' => 'BDT ' . number_format($registration->category_fee),
                        'Guest fee (each)' => $registration->guest_fee
                            ? 'BDT ' . number_format($registration->guest_fee)
                            : 'Guests not permitted in this category',
                    ],
                    'Personal' => [
                        'Blood group' => $registration->blood_group ?: '—',
                        'Present address' => $registration->present_address,
                        'Permanent address' => $registration->permanent_address ?: '—',
                    ],
                    'Academic' => [
                        'Session' => $registration->session
                            . ($registration->masters_session ? ' (Honours)' : ''),
                        'Masters session' => $registration->masters_session ?: '—',
                        'Degree' => $registration->degree_label,
                        'Passing year' => $registration->passing_year ?: 'Still studying',
                        'Class roll' => $registration->class_roll ?: '—',
                        'Registration no' => $registration->registration_no ?: '—',
                    ],
                    'Professional' => [
                        'Employment status' => $registration->employment_label,
                        'Profession / sector' => $registration->profession ?: '—',
                        'Designation' => $registration->designation ?: '—',
                        'Organization' => $registration->organization ?: '—',
                    ],
                    'Reunion' => [
                        'T-shirt size' => $registration->tshirt_size,
                        'Cultural programme' => $registration->cultural_program ? 'Yes — performing' : 'No',
                        'Accompanying guests' => $registration->guest_total,
                    ],
                ] as $heading => $rows)
                <div class="card overflow-hidden">
                    <h3
                        class="border-b border-ink-900/8 bg-parchment-dim px-6 py-3 font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">
                        {{ $heading }}
                    </h3>
                    <dl class="divide-y divide-ink-900/6">
                        @foreach ($rows as $label => $value)
                            <div class="flex flex-col gap-1 px-6 py-3 sm:flex-row sm:justify-between sm:gap-8">
                                <dt class="flex-none text-sm text-ink-500">{{ $label }}</dt>
                                <dd class="text-sm text-ink-900 sm:max-w-md sm:text-right">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endforeach

            @if ($registration->guests)
                <div class="card overflow-hidden">
                    <h3
                        class="border-b border-ink-900/8 bg-parchment-dim px-6 py-3 font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">
                        Guests
                    </h3>
                    <ul class="divide-y divide-ink-900/6">
                        @foreach ($registration->guests as $guest)
                            <li class="flex flex-wrap items-baseline gap-x-3 px-6 py-3 text-sm">
                                <span class="font-medium text-ink-900">{{ $guest['name'] }}</span>
                                @if (!empty($guest['relation']))<span class="text-ink-400">{{ $guest['relation'] }}</span>@endif
                                @if (!empty($guest['occupation']))<span class="text-ink-400">&middot;
                                {{ $guest['occupation'] }}</span>@endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($registration->memories)
                <div class="card p-6">
                    <h3 class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">Memories & remarks</h3>
                    <p class="prose-rc mt-3 whitespace-pre-line text-[0.95rem]">{{ $registration->memories }}</p>
                </div>
            @endif
        </div>

        {{-- Payment & actions --}}
        <div class="space-y-4 lg:sticky lg:top-24">
            <div class="card overflow-hidden">
                <h3
                    class="border-b border-ink-900/8 bg-parchment-dim px-5 py-3 font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">
                    Payment
                </h3>
                <dl class="divide-y divide-ink-900/6 text-sm">
                    @foreach ([
                            'Category' => $registration->category_label,
                            'Method' => $registration->payment_method_label,
                            'Transaction ID' => $registration->transaction_id,
                            'Sent from' => $registration->sender_number,
                            'Total fee' => 'BDT ' . number_format($registration->amount_due),
                            'Amount paid' => 'BDT ' . number_format($registration->amount_paid),
                        ] as $label => $value)
                        <div class="flex justify-between gap-3 px-5 py-2.5">
                            <dt class="text-ink-500">{{ $label }}</dt>
                            <dd class="text-right font-medium text-ink-900">{{ $value }}</dd>
                        </div>
                    @endforeach

                    @if ($registration->balance !== 0)
                        <div class="flex justify-between gap-3 px-5 py-2.5">
                            <dt class="text-ink-500">{{ $registration->balance < 0 ? 'Short by' : 'Overpaid by' }}</dt>
                            <dd @class([
                                'text-right font-semibold',
                                'text-red-600' => $registration->balance < 0,
                                'text-emerald-600' => $registration->balance > 0,
                            ])>BDT {{ number_format(abs($registration->balance)) }}</dd>
                        </div>
                    @endif
                </dl>

                {{-- The evidence, kept beside the figures it corroborates. --}}
                <div class="border-t border-ink-900/8 px-5 py-4">
                    <p class="font-mono text-[0.55rem] uppercase tracking-[0.16em] text-ink-400">Receipt</p>

                    @if ($registration->payment_receipt_url)
                        @if ($registration->payment_receipt_is_pdf)
                            <a href="{{ $registration->payment_receipt_url }}" target="_blank" rel="noopener"
                                class="mt-2 flex items-center gap-2.5 rounded-xl border border-ink-900/10 px-3 py-2.5 text-sm text-ink-800 transition hover:border-brass-500 hover:bg-brass-100">
                                <x-icon name="download" class="h-4 w-4 flex-none text-brass-600" />
                                Open the PDF receipt
                            </a>
                        @else
                            <a href="{{ $registration->payment_receipt_url }}" target="_blank" rel="noopener"
                                class="mt-2 block overflow-hidden rounded-xl border border-ink-900/10 transition hover:border-brass-500">
                                <img src="{{ $registration->payment_receipt_url }}"
                                    alt="Payment receipt for {{ $registration->reference }}"
                                    class="max-h-64 w-full bg-parchment-dim object-contain">
                            </a>
                            <p class="mt-2 text-center text-xs text-ink-400">Click to view full size</p>
                        @endif
                    @else
                        <p class="mt-2 text-sm text-ink-400">
                            None attached — verify the transaction ID against the statement,
                            or telephone {{ $registration->mobile }}.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Verification --}}
            <form method="POST" action="{{ route('admin.registrations.update', $registration) }}" class="card p-5">
                @csrf
                @method('PATCH')

                <h3 class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">Verification</h3>

                <div class="mt-4 space-y-2">
                    @foreach ([
                            'verified' => 'Verified — payment confirmed',
                            'pending' => 'Pending — not yet checked',
                            'rejected' => 'Rejected — could not be matched',
                        ] as $value => $label)
                        <label class="choice !py-2.5 !text-[0.8rem]">
                            <input type="radio" name="payment_status" value="{{ $value }}"
                                @checked($registration->payment_status === $value)>
                            <span class="choice-box" aria-hidden="true"></span>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4">
                    <label for="admin_note" class="field-label !text-xs">
                        Note to the registrant <span class="font-normal text-ink-400">(shown on their status
                            page)</span>
                    </label>
                    <textarea id="admin_note" name="admin_note" rows="3" class="input !text-sm"
                        placeholder="Optional">{{ old('admin_note', $registration->admin_note) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-sm mt-4 w-full">Save Status</button>

                @if ($registration->verified_at)
                    <p class="mt-3 text-center text-xs text-ink-400">
                        Verified {{ $registration->verified_at->format('j M Y, g:i a') }}
                        @if ($registration->verifier) by {{ $registration->verifier->name }} @endif
                    </p>
                @endif
            </form>

            {{-- Meta --}}
            <div class="card p-5 text-xs text-ink-400">
                <p>Submitted {{ $registration->created_at->format('j M Y, g:i a') }}</p>
                @if ($registration->ip_address)
                    <p class="mt-1">From {{ $registration->ip_address }}</p>
                @endif
            </div>

            {{-- Delete --}}
            <form method="POST" action="{{ route('admin.registrations.destroy', $registration) }}"
                onsubmit="return confirm('Delete registration {{ $registration->reference }}? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 px-4 py-2.5 text-xs font-semibold text-red-700 transition hover:bg-red-50">
                    <x-icon name="trash" class="h-3.5 w-3.5" />Delete this registration
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>