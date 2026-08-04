<x-layout :title="$title">
    <section data-theme="dark" class="relative overflow-hidden bg-ink-900 py-24 md:py-32">
        <div class="bg-grid-light pointer-events-none absolute inset-0"></div>
        <div class="pointer-events-none absolute -top-24 left-1/2 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-brass-700/20 blur-[120px]"></div>

        <div class="container-narrow relative text-center">
            <span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brass-500 text-ink-950"
                  data-reveal="scale">
                <x-icon name="check" class="h-8 w-8" stroke-width="2.4"/>
            </span>

            <h1 class="heading-display mt-8 text-[clamp(2rem,4.6vw,3.2rem)] text-parchment" data-reveal="split">
                Registration received
            </h1>

            <p class="prose-rc mx-auto mt-5 max-w-xl !text-ink-200" data-reveal data-reveal-delay="0.15">
                Thank you, {{ $registration->full_name_en }}. Your place at
                {{ config('rcmaa.registration.event_name') }} is reserved pending payment verification by
                the committee — normally one to two working days.
            </p>

            {{-- Reference --}}
            <div class="mx-auto mt-10 max-w-md rounded-2xl border border-white/12 bg-ink-800/60 p-7"
                 data-reveal data-reveal-delay="0.25">
                <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-400">
                    Your reference number
                </p>
                <p class="heading-display mt-3 text-4xl tracking-wider text-parchment">
                    {{ $registration->reference }}
                </p>
                <p class="mt-4 text-xs leading-relaxed text-ink-400">
                    Keep this safe. You will need it at the registration desk and to check your status online.
                    A copy has been emailed to {{ $registration->email }}.
                </p>
            </div>

            <div class="mt-9 flex flex-wrap justify-center gap-3" data-reveal data-reveal-delay="0.3">
                <a href="{{ route('portal.request') }}" class="btn btn-primary">Manage My Registration</a>
                <a href="{{ route('registration.status') }}" class="btn btn-outline-light">Check Status</a>
                <button type="button" onclick="window.print()" class="btn btn-outline-light">Print This Page</button>
            </div>
        </div>
    </section>

    {{-- Summary --}}
    <section class="bg-parchment py-16 md:py-24">
        <div class="container-narrow">
            <x-section-heading eyebrow="Summary" title="What we recorded" size="sm"/>

            <div class="card mt-10 divide-y divide-ink-900/8">
                @php
                    $rows = [
                        'Full name' => $registration->full_name_en.($registration->full_name_bn ? ' · '.$registration->full_name_bn : ''),
                        'Mobile' => $registration->mobile,
                        'Email' => $registration->email,
                        'Session' => $registration->session,
                        'Masters session' => $registration->masters_session ?: null,
                        'Degree' => $registration->degree_label,
                        'Passing year' => $registration->passing_year ?: 'Still studying',
                        'Employment' => $registration->employment_label,
                        'T-shirt size' => $registration->tshirt_size,
                        'Cultural programme' => $registration->cultural_program ? 'Yes — performing' : 'No',
                        'Accompanying guests' => $registration->guest_total,
                        'Payment method' => $registration->payment_method_label,
                        'Transaction ID' => $registration->transaction_id,
                        'Amount paid' => 'BDT '.number_format($registration->amount_paid),
                    ];
                @endphp

                @foreach ($rows as $label => $value)
                    <div class="flex flex-col gap-1 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <dt class="text-sm text-ink-500">{{ $label }}</dt>
                        <dd class="text-sm font-medium text-ink-900 sm:text-right">{{ $value }}</dd>
                    </div>
                @endforeach

                <div class="flex flex-col gap-1 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <dt class="text-sm text-ink-500">Status</dt>
                    <dd>
                        <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Awaiting verification
                        </span>
                    </dd>
                </div>
            </div>

            @if ($registration->guests)
                <div class="card mt-4 p-6">
                    <p class="field-label">Accompanying guests</p>
                    <ul class="space-y-2 text-sm text-ink-700">
                        @foreach ($registration->guests as $guest)
                            <li class="flex flex-wrap gap-x-2">
                                <span class="font-medium">{{ $guest['name'] }}</span>
                                @if (! empty($guest['relation']))<span class="text-ink-400">· {{ $guest['relation'] }}</span>@endif
                                @if (! empty($guest['occupation']))<span class="text-ink-400">· {{ $guest['occupation'] }}</span>@endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <x-alert type="info" class="mt-8">
                Spotted a mistake? Call the helpdesk on
                <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.contact.helpline')) }}" class="font-semibold underline underline-offset-2">{{ config('rcmaa.contact.helpline') }}</a>
                with your reference number — please do not submit a second registration.
            </x-alert>
        </div>
    </section>
</x-layout>
