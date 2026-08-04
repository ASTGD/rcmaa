@php
    $opt = config('rcmaa.options');
    $r = $registration;
    $states = [
        'pending' => ['Awaiting verification', 'bg-amber-100 text-amber-800', 'bg-amber-500'],
        'verified' => ['Verified — you\'re confirmed', 'bg-emerald-100 text-emerald-800', 'bg-emerald-500'],
        'rejected' => ['Could not be verified', 'bg-red-100 text-red-800', 'bg-red-500'],
    ];
    [$label, $chip, $dot] = $states[$r->payment_status] ?? $states['pending'];
@endphp

<x-layout :title="$title">
    <x-page-hero
        eyebrow="Your registration"
        :title="$r->full_name_en"
        :breadcrumbs="['My registration' => null]">
        <div class="mt-6 flex flex-wrap items-center gap-4" data-reveal data-reveal-delay="0.2">
            <span class="heading-display text-2xl tracking-wider text-brass-400">{{ $r->reference }}</span>
            <span class="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-semibold {{ $chip }}">
                <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>{{ $label }}
            </span>
        </div>
        <div class="mt-7 flex flex-wrap gap-3" data-reveal data-reveal-delay="0.3">
            <a href="{{ route('portal.pass') }}" class="btn btn-primary">
                <x-icon name="download" class="h-4 w-4"/>My entry pass
            </a>
            <form method="POST" action="{{ route('portal.close') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light">Sign out</button>
            </form>
        </div>
    </x-page-hero>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc grid gap-8 lg:grid-cols-[1fr_20rem] lg:items-start">
            <div class="min-w-0">
                @if (session('status'))
                    <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
                @endif
                @if ($r->admin_note)
                    <x-alert type="info" title="Note from the committee" class="mb-6">{{ $r->admin_note }}</x-alert>
                @endif

                <form method="POST" action="{{ route('portal.update') }}" class="card p-6 md:p-8">
                    @csrf @method('PATCH')

                    <h2 class="heading-display text-xl text-ink-950">Your details</h2>
                    <p class="prose-rc mt-1.5 text-sm">
                        Change anything here yourself. Your name, session and payment can only be
                        altered by the committee — call the helpdesk on {{ config('rcmaa.contact.hotline') }}.
                    </p>

                    <div class="mt-7 grid gap-6 sm:grid-cols-2">
                        <x-field name="mobile" :value="$r->mobile" label="Mobile" bn="মোবাইল" type="tel" required :model="false"/>
                        <x-field name="whatsapp" :value="$r->whatsapp" label="WhatsApp" type="tel" :model="false"/>
                        <x-field name="blood_group" :value="$r->blood_group" label="Blood group" type="select" :model="false"
                                 :options="array_combine($opt['blood_groups'], $opt['blood_groups'])"
                                 placeholder="Select blood group"/>
                        <x-field name="tshirt_size" :value="$r->tshirt_size" label="T-shirt size" type="select" required :model="false"
                                 :options="array_combine($opt['tshirt_sizes'], $opt['tshirt_sizes'])"/>
                        <x-field name="present_address" :value="$r->present_address" label="Present address" bn="বর্তমান ঠিকানা"
                                 type="textarea" rows="3" required :model="false" class="sm:col-span-2"/>
                        <x-field name="permanent_address" :value="$r->permanent_address" label="Permanent address" bn="স্থায়ী ঠিকানা"
                                 type="textarea" rows="3" :model="false" class="sm:col-span-2"/>
                        <x-field name="profession" :value="$r->profession" label="Profession / sector" :model="false"/>
                        <x-field name="designation" :value="$r->designation" label="Designation" bn="পদবী" :model="false"/>
                        <x-field name="organization" :value="$r->organization" label="Organization" bn="কর্মস্থল" :model="false" class="sm:col-span-2"/>
                        <x-field name="memories" :value="$r->memories" label="Your memories of the department" type="textarea"
                                 rows="5" :model="false" class="sm:col-span-2"/>
                    </div>

                    <div class="mt-7 rounded-2xl bg-parchment-dim p-5">
                        <label class="choice !items-start !border-0 !bg-transparent !py-0">
                            <input type="checkbox" name="listed_in_directory" value="1"
                                   @checked(old('listed_in_directory', $r->listed_in_directory))>
                            <span class="choice-box mt-0.5" aria-hidden="true"></span>
                            <span class="text-[0.85rem] font-normal leading-relaxed">
                                <strong class="font-semibold">Show me in the public alumni directory.</strong>
                                It lists your name, session, profession, photograph and mobile number so
                                fellow graduates can reach you. Your email and addresses are never published.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-7">Save changes</button>
                </form>
            </div>

            {{-- What only the committee can change --}}
            <aside class="space-y-4 lg:sticky lg:top-28">
                <div class="card overflow-hidden">
                    <h2 class="border-b border-ink-900/8 bg-parchment-dim px-5 py-3 font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">
                        Fixed by the committee
                    </h2>
                    <dl class="divide-y divide-ink-900/6 text-sm">
                        @foreach ([
                            'Category' => $r->category_label,
                            'Session' => $r->session,
                            'Degree' => $r->degree_label,
                            'Guests' => $r->guest_total,
                            'Amount due' => 'BDT '.number_format($r->amount_due),
                            'Amount paid' => 'BDT '.number_format($r->amount_paid),
                        ] as $k => $v)
                            <div class="flex justify-between gap-3 px-5 py-2.5">
                                <dt class="text-ink-500">{{ $k }}</dt>
                                <dd class="text-right font-medium text-ink-900">{{ $v }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                {{-- Receipt. Its own form, because it posts a file and nothing else. --}}
                <div class="card overflow-hidden">
                    <h2 class="border-b border-ink-900/8 bg-parchment-dim px-5 py-3 font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">
                        Payment receipt
                    </h2>

                    <div class="p-5">
                        @if ($r->payment_receipt_url)
                            @if ($r->payment_receipt_is_pdf)
                                <a href="{{ $r->payment_receipt_url }}" target="_blank" rel="noopener"
                                   class="flex items-center gap-2.5 rounded-xl border border-ink-900/10 px-3 py-2.5 text-sm text-ink-800 transition hover:border-brass-500 hover:bg-brass-100">
                                    <x-icon name="download" class="h-4 w-4 flex-none text-brass-600"/>
                                    Your receipt (PDF)
                                </a>
                            @else
                                <a href="{{ $r->payment_receipt_url }}" target="_blank" rel="noopener"
                                   class="block overflow-hidden rounded-xl border border-ink-900/10 transition hover:border-brass-500">
                                    <img src="{{ $r->payment_receipt_url }}" alt="Your payment receipt"
                                         class="max-h-48 w-full bg-parchment-dim object-contain">
                                </a>
                            @endif
                            <p class="mt-3 text-xs text-ink-400">
                                On file with the committee. Upload again to replace it.
                            </p>
                        @else
                            <p class="text-sm text-ink-500">
                                Nothing attached yet. A screenshot of your bKash confirmation
                                helps the committee verify you without telephoning.
                            </p>
                        @endif

                        <form method="POST" action="{{ route('portal.receipt') }}"
                              enctype="multipart/form-data" class="mt-4">
                            @csrf
                            <label for="portal-receipt"
                                   class="group flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed border-ink-900/15 px-4 py-3.5 transition hover:border-brass-500 hover:bg-brass-100">
                                <input id="portal-receipt" type="file" name="payment_receipt" class="sr-only"
                                       accept="image/jpeg,image/png,image/webp,application/pdf"
                                       onchange="this.form.querySelector('[data-receipt-name]').textContent = this.files[0]?.name ?? ''; this.form.querySelector('[data-receipt-submit]').hidden = ! this.files.length">
                                <span class="grid h-9 w-9 flex-none place-items-center rounded-lg bg-brass-100 text-brass-700 transition group-hover:bg-brass-500 group-hover:text-ink-950">
                                    <x-icon name="upload" class="h-4 w-4"/>
                                </span>
                                <span class="min-w-0 text-sm font-medium text-ink-800">
                                    {{ $r->payment_receipt_url ? 'Choose a replacement' : 'Choose a file' }}
                                    <span data-receipt-name class="block truncate text-xs font-normal text-brass-700"></span>
                                </span>
                            </label>

                            @error('payment_receipt')<p class="field-error">{{ $message }}</p>@enderror

                            <button type="submit" data-receipt-submit hidden class="btn btn-primary btn-sm mt-3 w-full">
                                Upload receipt
                            </button>
                            <p class="mt-2 text-xs text-ink-400">JPG, PNG, WebP or PDF &middot; maximum 4 MB</p>
                        </form>
                    </div>
                </div>

                <div class="card p-5">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-brass-700">Helpdesk</p>
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.contact.hotline')) }}"
                       class="mt-2 block text-base font-medium text-ink-900 transition hover:text-brass-700">
                        {{ config('rcmaa.contact.hotline') }}
                    </a>
                    <p class="mt-1 text-xs text-ink-400">{{ config('rcmaa.contact.hotline_hours') }}</p>
                </div>
            </aside>
        </div>
    </section>
</x-layout>
