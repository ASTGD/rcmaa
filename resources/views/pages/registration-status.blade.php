<x-layout :title="$title" :description="$description ?? null">
    <x-page-hero
        eyebrow="Registration"
        title="Check your status"
        lead="Enter the reference number from your confirmation email together with the mobile number you registered with."
        :breadcrumbs="['Registration status' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-narrow">

            <form method="POST" action="{{ route('registration.status.lookup') }}" class="card p-6 md:p-8">
                @csrf
                <div class="grid gap-5 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                    <div>
                        <label for="reference" class="field-label">Reference number</label>
                        <input id="reference" name="reference" type="text" class="input uppercase"
                               placeholder="RC26-XXXXXX" value="{{ old('reference') }}"
                               @error('reference') aria-invalid="true" @enderror required>
                        @error('reference')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="mobile" class="field-label">Mobile number</label>
                        <input id="mobile" name="mobile" type="tel" class="input" inputmode="tel"
                               placeholder="01712345678" value="{{ old('mobile') }}"
                               @error('mobile') aria-invalid="true" @enderror required>
                        @error('mobile')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn btn-ink h-[3.05rem]">
                        <x-icon name="search" class="h-4 w-4"/>
                        Look Up
                    </button>
                </div>
            </form>

            @if (! empty($searched))
                @if ($registration)
                    @php
                        $states = [
                            'pending' => ['Awaiting verification', 'bg-amber-100 text-amber-800', 'bg-amber-500', 'Your payment is queued for manual verification by the committee. This normally takes one to two working days.'],
                            'verified' => ['Verified — you\'re confirmed', 'bg-emerald-100 text-emerald-800', 'bg-emerald-500', 'Your payment has been verified and your seat is confirmed. Bring your reference number to the registration desk on the day.'],
                            'rejected' => ['Could not be verified', 'bg-red-100 text-red-800', 'bg-red-500', 'The committee could not match your payment. Please contact the helpdesk with your transaction details.'],
                        ];
                        [$label, $chip, $dot, $explain] = $states[$registration->payment_status] ?? $states['pending'];
                    @endphp

                    <div class="card mt-8 overflow-hidden">
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-ink-900/8 bg-parchment-dim px-6 py-5">
                            <div>
                                <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-700">Reference</p>
                                <p class="heading-display mt-1 text-2xl text-ink-950">{{ $registration->reference }}</p>
                            </div>
                            <span class="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-semibold {{ $chip }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
                                {{ $label }}
                            </span>
                        </div>

                        <div class="p-6">
                            <p class="prose-rc text-sm">{{ $explain }}</p>

                            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                                @foreach ([
                                    'Name' => $registration->full_name_en,
                                    'Session' => $registration->session.' · '.$registration->degree_label,
                                    'T-shirt size' => $registration->tshirt_size,
                                    'Guests' => $registration->guest_total,
                                    'Amount paid' => 'BDT '.number_format($registration->amount_paid),
                                    'Registered on' => $registration->created_at->format('j F Y'),
                                ] as $label => $value)
                                    <div class="rounded-xl bg-parchment px-4 py-3">
                                        <dt class="text-[0.7rem] uppercase tracking-wide text-ink-400">{{ $label }}</dt>
                                        <dd class="mt-1 text-sm font-medium text-ink-900">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>

                            @if ($registration->admin_note)
                                <x-alert type="info" title="Note from the committee" class="mt-6">
                                    {{ $registration->admin_note }}
                                </x-alert>
                            @endif
                        </div>
                    </div>
                @else
                    <x-empty-state class="mt-8" icon="search"
                        title="No registration found"
                        message="We couldn't find a registration matching that reference number and mobile number. Check both for typos, or contact the helpdesk for assistance.">
                        <a href="{{ route('help-center') }}" class="btn btn-outline btn-sm mt-6">Contact the Helpdesk</a>
                    </x-empty-state>
                @endif
            @endif

        </div>
    </section>
</x-layout>
