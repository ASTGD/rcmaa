<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Contact"
        title="Get in touch with the committee"
        lead="Whether you are an alumnus, a current student, or a visitor — questions about membership, the reunion, sponsorship or the directory are all welcome here."
        :breadcrumbs="['Contact' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc grid gap-12 lg:grid-cols-[1fr_22rem] lg:gap-16">

            {{-- Form --}}
            <div class="min-w-0">
                @if (session('status'))
                    <x-alert type="success" title="Message sent" class="mb-8">{{ session('status') }}</x-alert>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="card relative p-6 md:p-8">
                    @csrf

                    <div class="absolute -left-[9999px]" aria-hidden="true">
                        <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-field name="name" label="Your name" required :model="false" placeholder="Full name"/>
                        <x-field name="email" label="Email address" type="email" required :model="false"
                                 placeholder="you@example.com"/>
                        <x-field name="phone" label="Phone" type="tel" :model="false" placeholder="Optional"/>
                        <x-field name="subject" label="Subject" required :model="false"
                                 placeholder="What is this about?"/>
                        <x-field name="message" label="Message" type="textarea" rows="6" required :model="false"
                                 class="sm:col-span-2"
                                 hint="Please include your session and passing year if your question is about registration."/>
                    </div>

                    <button type="submit" class="btn btn-primary mt-8">
                        Send Message
                        <x-icon name="arrow-right" class="h-4 w-4"/>
                    </button>
                </form>

                {{-- Donations panel --}}
                @php $bank = array_filter(config('rcmaa.donation.bank')); @endphp
                <div class="mt-8 rounded-xl border border-brass-500/40 bg-brass-100/60 p-5 md:p-6"
                     x-data="{ showDonation: false }">
                    <p lang="bn" class="font-bangla text-[0.9rem] font-semibold text-ink-900">
                        ডোনেশন <span class="font-sans text-ink-500">&middot; Donation</span>
                    </p>
                    <p lang="bn" class="mt-1.5 font-bangla text-[0.84rem] leading-relaxed text-ink-700">
                        {{ config('rcmaa.donation.instruction_bn') }}
                    </p>
                    <p class="mt-2 text-[0.8rem] leading-relaxed text-ink-500">
                        {{ config('rcmaa.donation.instruction') }}
                    </p>

                    <button type="button" @click="showDonation = ! showDonation"
                            class="btn btn-ink btn-sm mt-3"
                            :aria-expanded="showDonation">
                        <x-icon name="heart" class="h-4 w-4"/>
                        Donation
                        <x-icon name="chevron-down" class="h-3.5 w-3.5 transition-transform"
                                ::class="showDonation && 'rotate-180'"/>
                    </button>

                    <div x-show="showDonation" x-collapse x-cloak>
                        <div class="mt-4 rounded-lg bg-white p-4">
                            <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-brass-700">
                                Bank account details <span lang="bn" class="font-bangla normal-case tracking-normal">&middot; ব্যাংক অ্যাকাউন্ট</span>
                            </p>

                            @if ($bank)
                                <dl class="mt-3 space-y-2">
                                    @foreach ([
                                        'account_name' => 'Account name',
                                        'account_number' => 'Account number',
                                        'bank' => 'Bank',
                                        'branch' => 'Branch',
                                        'routing' => 'Routing number',
                                        'swift_code' => 'Swift code',
                                    ] as $field => $label)
                                        @if ($bank[$field] ?? null)
                                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-baseline gap-1 sm:gap-4 border-b border-ink-900/5 pb-2 last:border-0 last:pb-0">
                                                <dt class="text-xs text-ink-500 font-medium sm:text-[0.85rem]">{{ $label }}</dt>
                                                <dd class="font-semibold text-ink-950 text-sm sm:text-[0.85rem] text-left sm:text-right break-words">{{ $bank[$field] }}</dd>
                                            </div>
                                        @endif
                                    @endforeach
                                </dl>
                            @else
                                <p lang="bn" class="mt-2 font-bangla text-[0.84rem] leading-relaxed text-ink-700">
                                    ব্যাংক অ্যাকাউন্টের বিবরণ শীঘ্রই জানানো হবে। এর মধ্যে ডোনেশনের জন্য
                                    হেল্পলাইনে যোগাযোগ করুন: {{ config('rcmaa.contact.helpline') }}
                                </p>
                                <p class="mt-1.5 text-[0.78rem] text-ink-500">
                                    Bank details will be announced shortly. Until then, please contact
                                    the helpline about donations: {{ config('rcmaa.contact.helpline') }}.
                                </p>
                            @endif

                            <p lang="bn" class="mt-4 border-t border-ink-900/8 pt-3 font-bangla text-[0.82rem] font-semibold text-ink-800">
                                {{ config('rcmaa.donation.note_bn') }}
                            </p>
                            <p class="mt-1 text-[0.76rem] text-ink-500">{{ config('rcmaa.donation.note') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <aside class="space-y-4">
                {{-- The association's own published channels and hours. --}}
                @foreach (config('rcmaa.contact_channels') as $channel)
                    <div class="card p-5" data-reveal>
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-mono text-[0.6rem] uppercase tracking-[0.18em] text-brass-700">
                                {{ $channel['label'] }}
                            </p>
                            <span class="flex items-center gap-1.5 text-[0.68rem] text-ink-400">
                                <x-icon name="clock" class="h-3 w-3"/>{{ $channel['hours'] }}
                            </span>
                        </div>

                        @if ($channel['phone'])
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $channel['phone']) }}"
                               class="mt-2.5 flex items-center gap-2.5 text-sm font-medium text-ink-900 transition hover:text-brass-700">
                                <x-icon name="phone" class="h-4 w-4 flex-none text-brass-600"/>{{ $channel['phone'] }}
                            </a>
                        @endif

                        @if (! empty($channel['whatsapp']))
                            <a href="https://wa.me/88{{ preg_replace('/\D/', '', $channel['whatsapp']) }}"
                               target="_blank" rel="noopener"
                               class="mt-1.5 flex items-center gap-2.5 text-sm text-ink-600 transition hover:text-brass-700">
                                <x-icon name="whatsapp" class="h-4 w-4 flex-none text-brass-600"/>
                                WhatsApp {{ $channel['whatsapp'] }}
                            </a>
                        @endif

                        @if ($channel['email'])
                            <a href="mailto:{{ $channel['email'] }}"
                               class="mt-1.5 flex items-center gap-2.5 text-sm text-ink-600 transition hover:text-brass-700">
                                <x-icon name="mail" class="h-4 w-4 flex-none text-brass-600"/>
                                <span class="truncate">{{ $channel['email'] }}</span>
                            </a>
                        @endif
                    </div>
                @endforeach

                <div class="card flex gap-4 p-5" data-reveal>
                    <span class="grid h-10 w-10 flex-none place-items-center rounded-xl bg-brass-100 text-brass-700">
                        <x-icon name="map-pin" class="h-4.5 w-4.5"/>
                    </span>
                    <div class="min-w-0">
                        <p class="font-mono text-[0.6rem] uppercase tracking-[0.18em] text-brass-700">Location</p>
                        <a href="{{ config('rcmaa.contact.map') }}" target="_blank" rel="noopener"
                           class="mt-1 block text-sm text-ink-800 transition hover:text-brass-700">
                            {{ config('rcmaa.contact.address') }}
                        </a>
                    </div>
                </div>

                <div data-theme="dark" class="rounded-2xl bg-ink-900 p-6" data-reveal>
                    <p class="font-mono text-[0.6rem] uppercase tracking-[0.18em] text-brass-400">Follow us</p>
                    @include('partials.social', ['class' => 'mt-4 text-ink-200'])
                    <p class="mt-5 text-xs leading-relaxed text-ink-400">
                        Batch groups and event updates are posted to our Facebook page first.
                    </p>
                </div>
            </aside>
        </div>
    </section>
</x-layout>
