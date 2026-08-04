<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Membership"
        title="How to apply"
        lead="Joining RCMAA and registering for the Grand Reunion are the same process — one form, one payment, one reference number."
        :breadcrumbs="['How to Apply' => null]"/>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc">
            {{-- Eligibility --}}
            <div class="grid gap-12 lg:grid-cols-[1fr_1.2fr] lg:gap-20">
                <div>
                    <x-section-heading eyebrow="Eligibility" title="Who can apply" size="sm"/>
                    <p class="prose-rc mt-5">
                        Any graduate of the Department of Mathematics, Rajshahi College is eligible —
                        whichever degree you completed and however long ago.
                    </p>
                </div>

                <ul class="space-y-3" data-reveal data-reveal-stagger="0.08">
                    @foreach (config('rcmaa.options.degrees') as $label)
                        <li class="card flex items-center gap-4 p-5" data-reveal-item>
                            <span class="grid h-9 w-9 flex-none place-items-center rounded-full bg-brass-500 text-ink-950">
                                <x-icon name="check" class="h-4 w-4"/>
                            </span>
                            <span class="text-[0.95rem] font-medium text-ink-900">{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- What you need --}}
            <div class="mt-20">
                <x-section-heading eyebrow="Before you start" title="What to have ready"/>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal data-reveal-stagger="0.1">
                    @foreach ([
                        ['user', 'Your details', 'Name in English and Bangla, mobile number, email, and your present address.'],
                        ['graduation', 'Academic record', 'Your session and passing year. Roll and registration numbers are optional.'],
                        ['camera', 'A passport photo', 'JPG, PNG or WebP, no larger than 1 MB. Used on your reunion identity card.'],
                        ['heart', 'Payment ready', 'A bKash, Nagad, Rocket or bank transfer, and the transaction ID from the confirmation SMS.'],
                    ] as [$icon, $heading, $body])
                        <article class="card p-7" data-reveal-item>
                            <span class="grid h-12 w-12 place-items-center rounded-xl bg-ink-900 text-brass-500">
                                <x-icon :name="$icon" class="h-5 w-5"/>
                            </span>
                            <h3 class="heading-display mt-6 text-lg text-ink-950">{{ $heading }}</h3>
                            <p class="prose-rc mt-2 text-sm">{{ $body }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            {{-- The form, section by section --}}
            <div class="mt-20">
                <x-section-heading eyebrow="The form" title="What the seven parts cover"
                    lead="The online form follows the association's printed registration form exactly, so anything you have already filled in on paper carries straight across."/>

                <div class="mt-12 grid gap-x-12 gap-y-8 md:grid-cols-2" data-reveal data-reveal-stagger="0.06">
                    @foreach ([
                        ['1', 'Personal Details', 'ব্যক্তিগত তথ্য', 'Name, blood group, mobile, WhatsApp, email, and both addresses.'],
                        ['2', 'Academic Information', 'শিক্ষা সংক্রান্ত তথ্য', 'Session, degree completed at Rajshahi College, roll and registration numbers, passing year.'],
                        ['3', 'Professional Details', 'পেশাগত তথ্য', 'Employment status, and if you are working, your profession, designation and organisation.'],
                        ['4', 'Reunion & Event Details', 'অনুষ্ঠান সংক্রান্ত', 'T-shirt size, whether you will perform in the cultural programme, and any accompanying guests.'],
                        ['5', 'Memories & Remarks', 'স্মৃতিচারণ', 'Anything you would like to share about the department — in English or Bangla.'],
                        ['6', 'Photograph', 'ছবি', 'Your passport-size photograph, up to 1 MB.'],
                        ['7', 'Payment & Verification', 'পেমেন্ট ও ভেরিফিকেশন', 'The amount you paid, the method used, the transaction ID and the number you sent it from.'],
                    ] as [$n, $en, $bn, $body])
                        <div class="flex gap-5 border-t border-ink-900/10 pt-6" data-reveal-item>
                            <span class="heading-display flex-none text-2xl text-brass-500">{{ $n }}</span>
                            <div>
                                <h3 class="heading-display text-base text-ink-950">{{ $en }}</h3>
                                <p lang="bn" class="mt-0.5 text-xs text-ink-400">{{ $bn }}</p>
                                <p class="prose-rc mt-2 text-sm">{{ $body }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('partials.cta')
</x-layout>
