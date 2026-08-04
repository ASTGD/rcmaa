@php
    $opt = config('rcmaa.options');
    $categories = config('rcmaa.registration.categories');
    $guestFee = config('rcmaa.registration.guest_fee');
    $cheapest = collect($categories)->min('fee');
    $methods = config('rcmaa.payment.methods');

    $steps = [
        1 => ['Category', 'শ্রেণী'],
        2 => ['Personal', 'ব্যক্তিগত তথ্য'],
        3 => ['Academic', 'শিক্ষা সংক্রান্ত'],
        4 => ['Professional', 'পেশাগত তথ্য'],
        5 => ['Reunion', 'অনুষ্ঠান সংক্রান্ত'],
        6 => ['Memories', 'স্মৃতিচারণ'],
        7 => ['Payment', 'পেমেন্ট'],
    ];
@endphp

<x-layout :title="$title" :description="$description">
    <x-page-hero
        eyebrow="Grand Reunion 2026"
        title="Reunion Registration"
        lead="Complete the form below to reserve your place at Math Nexus. It takes about five minutes, and your answers are saved in this browser as you go."
        :breadcrumbs="['Register' => null]">

        <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-ink-300" data-reveal data-reveal-delay="0.3">
            <span class="flex items-center gap-2">
                <x-icon name="calendar" class="h-4 w-4 text-brass-500"/>
                {{ \Carbon\Carbon::parse(config('rcmaa.registration.event_date'))->format('j F Y') }}
            </span>
            <span class="flex items-center gap-2">
                <x-icon name="clock" class="h-4 w-4 text-brass-500"/>
                Closes {{ \Carbon\Carbon::parse(config('rcmaa.registration.deadline'))->format('j F Y') }}
            </span>
            <span class="flex items-center gap-2">
                <x-icon name="user" class="h-4 w-4 text-brass-500"/>
From BDT {{ number_format($cheapest) }} &mdash; priced by category
            </span>
        </div>
    </x-page-hero>

    <section class="bg-parchment py-16 md:py-24">
        <div class="container-rc">

            @unless (config('rcmaa.registration.open'))
                <x-alert type="info" title="Registration is currently closed" class="mx-auto max-w-3xl">
                    Online registration for the Grand Reunion is not open at the moment. Please contact the
                    helpdesk on {{ config('rcmaa.contact.hotline') }} if you believe this is an error.
                </x-alert>
            @else

            @if ($errors->any())
                <x-alert type="error" title="Please review the highlighted fields" class="mx-auto mb-8 max-w-3xl">
                    {{ $errors->count() }} field(s) need attention. We've reopened the first step that needs a correction.
                </x-alert>
            @endif

            <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data"
                  x-data="registrationForm({
                      categories: {{ Js::from($categories) }},
                      guestFee: {{ Js::from($guestFee) }},
                      serverErrors: {{ Js::from($errors->messages()) }},
                      old: {{ Js::from(old()) }},
                  })"
                  @submit="submit($event)"
                  class="mx-auto grid max-w-6xl gap-10 lg:grid-cols-[1fr_20rem] lg:items-start">
                @csrf

                {{-- Honeypot: off-screen, never focusable, never announced. --}}
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                {{-- ======================= Main column ======================= --}}
                <div class="min-w-0">
                    {{-- Progress rail --}}
                    <div class="mb-8">
                        <div class="flex items-center justify-between text-xs text-ink-500">
                            <span class="font-mono uppercase tracking-[0.16em]">
                                Step <span x-text="step"></span> of <span x-text="totalSteps"></span>
                            </span>
                            <span x-text="progress + '% complete'"></span>
                        </div>

                        <div class="mt-3 h-1 overflow-hidden rounded-full bg-ink-900/10">
                            <div class="h-full rounded-full bg-brass-500 transition-[width] duration-700 ease-[cubic-bezier(.22,1,.36,1)]"
                                 :style="`width: ${Math.max(progress, 4)}%`"></div>
                        </div>

                        <ol class="mt-6 hidden flex-wrap gap-x-1 gap-y-2 md:flex">
                            @foreach ($steps as $number => [$en, $bnLabel])
                                <li>
                                    <button type="button" @click="goTo({{ $number }})"
                                            class="flex items-center gap-2 rounded-full px-3 py-1.5 text-[0.72rem] font-medium transition-colors"
                                            :class="step === {{ $number }}
                                                ? 'bg-ink-900 text-parchment'
                                                : (step > {{ $number }} ? 'text-brass-700 hover:bg-brass-100' : 'text-ink-400 hover:bg-ink-900/5')">
                                        <span class="grid h-4 w-4 place-items-center rounded-full text-[0.6rem] font-mono"
                                              :class="step > {{ $number }} ? 'bg-brass-500 text-ink-950' : 'bg-current/15'">
                                            <template x-if="step > {{ $number }}">
                                                <svg viewBox="0 0 24 24" class="h-2.5 w-2.5" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 13 4.5 4.5L19 7"/></svg>
                                            </template>
                                            <template x-if="step <= {{ $number }}"><span>{{ $number }}</span></template>
                                        </span>
                                        {{ $en }}
                                    </button>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="card p-6 md:p-9" data-step-panel>

                        {{-- ---------- Step 1 · Category ---------- --}}
                        <div x-show="step === 1" x-cloak>
                            @include('partials.register.heading', ['n' => 1, 'en' => 'Choose Your Category', 'bn' => 'আপনার শ্রেণী নির্বাচন করুন'])

                            <p lang="bn" class="-mt-4 font-bangla text-[0.95rem] leading-relaxed text-ink-700">
                                গণিত বিভাগের প্রথম রিইউনিয়নে (Math Nexus - RCMAA Reunion 2026)
                            </p>
                            <p class="prose-rc mb-7 mt-2 text-sm">
                                Registration is priced by category. Pick the one that describes you; the fee and
                                whether you may bring guests follow from it.
                            </p>

                            <div class="grid gap-4 sm:grid-cols-2">
                                @foreach ($categories as $key => $cat)
                                    <label class="group relative flex cursor-pointer flex-col rounded-2xl border p-6 transition-all duration-300"
                                           :class="form.category === '{{ $key }}'
                                               ? 'border-ink-900 bg-brass-100 ring-2 ring-brass-500'
                                               : 'border-ink-900/12 bg-white hover:border-brass-500 hover:bg-brass-100/40'">
                                        <input type="radio" name="category" value="{{ $key }}" class="sr-only"
                                               x-model="form.category" @checked(old('category') === $key)>

                                        <div class="flex items-start justify-between gap-3">
                                            <span class="grid h-11 w-11 flex-none place-items-center rounded-xl transition-colors duration-300"
                                                  :class="form.category === '{{ $key }}' ? 'bg-ink-900 text-brass-500' : 'bg-ink-900/5 text-brass-700'">
                                                <x-icon :name="$cat['icon']" class="h-5 w-5"/>
                                            </span>
                                            <span lang="bn" class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-brass-700">
                                                ক্যাটাগরি- {{ $cat['number'] }}
                                            </span>
                                        </div>

                                        <h3 lang="bn" class="mt-5 font-bangla text-lg font-semibold text-ink-950">
                                            {{ $cat['label_bn'] }}
                                        </h3>
                                        <p class="text-[0.78rem] text-ink-500">{{ $cat['label'] }}</p>

                                        <p lang="bn" class="mt-3 font-bangla text-[0.84rem] leading-relaxed text-ink-600">
                                            {{ $cat['blurb_bn'] }}
                                        </p>

                                        @if (! empty($cat['qualifies_bn']))
                                            {{-- Categories 3 and 4 carry several lines of session rules, and
                                                 they were most of what made step 1 nearly three screens on a
                                                 phone. Folded away by default, one tap to read. The toggle
                                                 sits inside the card's <label>, so its click must be stopped
                                                 or opening the rules would also select the category. --}}
                                            <div x-data="{ open: false }" class="mt-3">
                                                <button type="button" @click.stop.prevent="open = ! open"
                                                        :aria-expanded="open ? 'true' : 'false'"
                                                        aria-controls="eligibility-{{ $key }}"
                                                        class="flex w-full items-center justify-between gap-2 rounded-lg bg-ink-900/4 px-3 py-2.5 text-left transition-colors hover:bg-ink-900/8">
                                                    <span lang="bn" class="font-bangla text-[0.78rem] font-semibold text-ink-700">
                                                        {{ $cat['qualifies_bn'] }}
                                                    </span>
                                                    <span class="flex-none text-brass-700 transition-transform duration-300"
                                                          :class="open && 'rotate-180'">
                                                        <x-icon name="chevron-down" class="h-3.5 w-3.5"/>
                                                    </span>
                                                </button>

                                                <div id="eligibility-{{ $key }}" x-show="open" x-collapse x-cloak
                                                     lang="bn" class="rounded-b-lg bg-ink-900/4 px-3 pb-2.5 font-bangla">
                                                    <p class="text-[0.76rem] leading-relaxed text-ink-500">{{ $cat['qualifies_note_bn'] }}</p>
                                                    @foreach ((array) $cat['eligibility_bn'] as $line)
                                                        <p class="mt-1.5 text-[0.76rem] leading-relaxed text-ink-500">{{ $line }}</p>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @elseif (! empty($cat['eligibility_bn']))
                                            <div lang="bn" class="mt-2 rounded-lg bg-ink-900/4 px-3 py-2 font-bangla">
                                                @foreach ((array) $cat['eligibility_bn'] as $line)
                                                    <p class="text-[0.78rem] leading-relaxed text-ink-500">{{ $line }}</p>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mt-auto pt-5">
                                            <div class="rule-brass mb-4"></div>
                                            <p class="font-mono text-[0.6rem] uppercase tracking-[0.16em] text-brass-700">Registration fee</p>
                                            <p class="heading-display mt-1 text-3xl text-ink-950">
                                                &#2547;{{ number_format($cat['fee']) }}
                                            </p>

                                            @if ($cat['allows_guests'])
                                                <p class="mt-2 flex items-center gap-1.5 text-[0.74rem] text-ink-500">
                                                    <x-icon name="users" class="h-3.5 w-3.5 text-brass-600"/>
                                                    Can add guests &mdash; &#2547;{{ number_format($guestFee) }} each
                                                </p>
                                            @else
                                                <p class="mt-2 flex items-center gap-1.5 text-[0.74rem] text-ink-400">
                                                    <x-icon name="user" class="h-3.5 w-3.5"/>
                                                    Individual registration only
                                                </p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <p class="field-error mt-4" x-show="errors.category" x-text="errors.category" x-cloak></p>
                            @error('category')<p class="field-error mt-4">{{ $message }}</p>@enderror
                        </div>

                        {{-- ---------- Step 2 · Personal ---------- --}}
                        <div x-show="step === 2" x-cloak>
                            @include('partials.register.heading', ['n' => 2, 'en' => 'Personal Details', 'bn' => 'ব্যক্তিগত তথ্য'])

                            <div class="grid gap-6 sm:grid-cols-2">
                                <x-field name="full_name_en" autocomplete="name" label="Full Name (English)" required
                                         placeholder="e.g. Md. Rofikul Islam"/>
                                <x-field name="full_name_bn" label="Full Name" bn="বাংলায় নাম"
                                         placeholder="মোঃ রফিকুল ইসলাম"/>

                                <x-field name="mobile" autocomplete="tel" label="Mobile / Contact No" bn="মোবাইল" type="tel" required
                                         placeholder="01712345678"/>
                                <x-field name="whatsapp" autocomplete="tel" label="WhatsApp Number" type="tel"
                                         placeholder="Leave blank if same as mobile"/>

                                <x-field name="email" autocomplete="email" label="Email Address" type="email" required
                                         placeholder="you@example.com"
                                         hint="Your reference number and confirmation are sent here."/>
                                <x-field name="blood_group" label="Blood Group" type="select"
                                         :options="array_combine($opt['blood_groups'], $opt['blood_groups'])"
                                         placeholder="Select blood group"
                                         hint="Optional, but useful for the on-site medical desk."/>

                                <x-field name="present_address" autocomplete="street-address" label="Present Address" bn="বর্তমান ঠিকানা"
                                         type="textarea" rows="3" required class="sm:col-span-2"/>
                                <x-field name="permanent_address" label="Permanent Address" bn="স্থায়ী ঠিকানা"
                                         type="textarea" rows="3" class="sm:col-span-2"/>
                            </div>
                            {{-- Publishing a mobile number is a decision the registrant should
                                 make knowingly, so it is stated at the point of entry. --}}
                            <x-alert type="info" title="Your mobile number will be published" class="mt-6">
                                Verified registrations appear in the public
                                <a href="{{ route('directory') }}" class="font-semibold underline underline-offset-2">alumni directory</a>,
                                which shows your name, session, profession, photograph and mobile number so
                                that fellow graduates can reach you. Your email, addresses and blood group are
                                <strong>not</strong> published. You can ask us to remove your number at any time.
                            </x-alert>

                        </div>

                        {{-- ---------- Step 2 · Academic ---------- --}}
                        <div x-show="step === 3" x-cloak>
                            @include('partials.register.heading', ['n' => 3, 'en' => 'Academic Information', 'bn' => 'শিক্ষা সংক্রান্ত তথ্য'])

                            <div class="grid gap-6 sm:grid-cols-2">
                                <x-field name="session" label="Session" bn="অনার্স / মাস্টার্স সেশন" required
                                         type="select" :options="$opt['sessions']"
                                         placeholder="Select your session…"
                                         hint="The session you were admitted in, not the year you passed."/>
                                <x-field name="passing_year" label="Passing Year" type="number" required
                                         placeholder="{{ now()->year - 10 }}"/>

                                <x-choice-group name="degree" label="Degree Completed from Rajshahi College" required
                                                :options="$opt['degrees']" cols="sm:grid-cols-2"
                                                class="sm:col-span-2"/>

                                <x-field name="class_roll" label="Class Roll" bn="যদি মনে থাকে"
                                         placeholder="Optional"/>
                                <x-field name="registration_no" label="Registration No" bn="যদি মনে থাকে"
                                         placeholder="Optional"/>
                            </div>

                            <p class="field-hint mt-6">
                                Roll and registration numbers are optional — many alumni no longer have their
                                records, and your session and passing year are enough for verification.
                            </p>
                        </div>

                        {{-- ---------- Step 3 · Professional ---------- --}}
                        <div x-show="step === 4" x-cloak>
                            @include('partials.register.heading', ['n' => 4, 'en' => 'Professional Details', 'bn' => 'পেশাগত তথ্য'])

                            <x-choice-group name="employment_status" label="Employment Status" bn="কর্মসংস্থান" required
                                            :options="collect($opt['employment_statuses'])
                                                ->map(fn ($l, $k) => $l.' · '.$opt['employment_statuses_bn'][$k])
                                                ->all()"
                                            cols="sm:grid-cols-3"/>

                            {{-- Only relevant to people who said they work. --}}
                            <div x-show="['employed','self_employed'].includes(form.employment_status)" x-collapse>
                                <div class="mt-8 grid gap-6 sm:grid-cols-2">
                                    <x-field name="profession" label="Profession / Sector" required
                                             placeholder="e.g. Education, Banking, Civil Service"/>
                                    <x-field name="designation" autocomplete="organization-title" label="Designation" bn="পদবী"
                                             placeholder="e.g. Assistant Professor"/>
                                    <x-field name="organization" autocomplete="organization" label="Organization / Institution" bn="কর্মস্থল"
                                             required class="sm:col-span-2"
                                             placeholder="e.g. Rajshahi College"/>
                                </div>
                            </div>

                            <p class="field-hint mt-6" x-show="form.employment_status === 'student_other'" x-cloak>
                                No further details needed — continue to the next step.
                            </p>
                        </div>

                        {{-- ---------- Step 4 · Reunion & guests ---------- --}}
                        <div x-show="step === 5" x-cloak>
                            @include('partials.register.heading', ['n' => 5, 'en' => 'Reunion & Event Details', 'bn' => 'অনুষ্ঠান সংক্রান্ত'])

                            <div class="space-y-8">
                                <x-choice-group name="tshirt_size" label="T-Shirt Size" required
                                                :options="array_combine($opt['tshirt_sizes'], $opt['tshirt_sizes'])"
                                                cols="sm:grid-cols-6"/>

                                <x-choice-group name="cultural_program" required
                                                label="Interested to participate in the Cultural Program?"
                                                :options="['1' => 'Yes', '0' => 'No']"
                                                cols="sm:grid-cols-2"
                                                hint="Prior registration is mandatory to perform on the event day."/>

                                {{-- Fallback for the case where no radio below is checked.
                                     It MUST stay above the group: x-show only hides the
                                     radios, it does not stop them being submitted, so the
                                     browser sends both and PHP keeps the last one. Putting
                                     this first lets a real choice win. GuestCountTest locks
                                     the ordering in place. --}}
                                <input type="hidden" name="guest_count" value="0">

                                <div x-show="allowsGuests" x-collapse>
                                    <x-choice-group name="guest_count" label="Accompanying Guests / Spouse" bn="অতিথি সংখ্যা"
                                                    required :options="$opt['guest_counts']" cols="sm:grid-cols-4"
                                                    :hint="'BDT '.number_format($guestFee).' per guest, added to your total automatically.'"/>
                                </div>

                                {{-- Categories 3 and 4 are individual registrations. --}}
                                <div x-show="! allowsGuests && form.category" x-cloak>
                                    <x-alert type="info" title="Individual registration">
                                        The <span x-text="category?.label"></span> category does not include
                                        accompanying guests. If you need to bring someone, contact the helpdesk on
                                        {{ config('rcmaa.contact.hotline') }}.
                                    </x-alert>
                                </div>

                                {{-- Guest repeater --}}
                                <div x-show="allowsGuests && guestTotal > 0" x-collapse>
                                    <div class="rounded-2xl bg-parchment-dim p-5 md:p-6">
                                        <p class="field-label !mb-4">Guest details</p>

                                        <div class="space-y-4">
                                            <template x-for="(guest, index) in form.guests" :key="index">
                                                <div class="rounded-xl border border-ink-900/8 bg-white p-4">
                                                    <div class="mb-3 flex items-center justify-between">
                                                        <span class="font-mono text-[0.66rem] uppercase tracking-[0.16em] text-brass-700"
                                                              x-text="'Guest ' + (index + 1)"></span>
                                                        <button type="button" @click="removeGuest(index)"
                                                                x-show="form.guest_count === '3+'"
                                                                class="text-ink-400 transition hover:text-red-600"
                                                                :aria-label="'Remove guest ' + (index + 1)">
                                                            <x-icon name="trash" class="h-4 w-4"/>
                                                        </button>
                                                    </div>

                                                    <div class="grid gap-3 sm:grid-cols-3">
                                                        <div>
                                                            <label class="field-label !text-[0.72rem]" :for="'guest-name-'+index">Name</label>
                                                            <input :id="'guest-name-'+index" type="text" class="input"
                                                                   :name="`guests[${index}][name]`" x-model="guest.name"
                                                                   :aria-invalid="errors[`guests.${index}.name`] ? 'true' : 'false'">
                                                            <p class="field-error" x-show="errors[`guests.${index}.name`]"
                                                               x-text="errors[`guests.${index}.name`]"></p>
                                                        </div>
                                                        <div>
                                                            <label class="field-label !text-[0.72rem]" :for="'guest-rel-'+index">Relation</label>
                                                            <input :id="'guest-rel-'+index" type="text" class="input"
                                                                   :name="`guests[${index}][relation]`" x-model="guest.relation"
                                                                   placeholder="e.g. Spouse">
                                                        </div>
                                                        <div>
                                                            <label class="field-label !text-[0.72rem]" :for="'guest-occ-'+index">Occupation</label>
                                                            <input :id="'guest-occ-'+index" type="text" class="input"
                                                                   :name="`guests[${index}][occupation]`" x-model="guest.occupation">
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <button type="button" @click="addGuest()" x-show="form.guest_count === '3+'"
                                                class="btn btn-outline btn-sm mt-4">
                                            <x-icon name="plus" class="h-3.5 w-3.5"/>
                                            Add another guest
                                        </button>

                                        @error('guests')
                                            <p class="field-error mt-3">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ---------- Step 5 · Memories & photo ---------- --}}
                        <div x-show="step === 6" x-cloak>
                            @include('partials.register.heading', ['n' => 6, 'en' => 'Memories & Photograph', 'bn' => 'স্মৃতিচারণ ও ছবি'])

                            <x-field name="memories" type="textarea" rows="7"
                                     label="Share a memorable moment or a comment about the Department of Mathematics / Rajshahi College"
                                     bn="ইংরেজি বা বাংলায় লিখতে পারেন"
                                     placeholder="Your teachers, your classroom, the friends you made — anything you would like the committee to read."/>

                            <div class="mt-8">
                                <p class="field-label">
                                    Passport-size photograph
                                    <span lang="bn" class="field-label-bn"> &middot; পাসপোর্ট সাইজ ছবি</span>
                                </p>

                                <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                                    <label class="group flex flex-1 cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-ink-900/15 bg-white px-6 py-10 text-center transition hover:border-brass-500 hover:bg-brass-100">
                                        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                                               class="sr-only" @change="handlePhoto($event)">
                                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-brass-100 text-brass-700 transition group-hover:bg-brass-500 group-hover:text-ink-950">
                                            <x-icon name="upload" class="h-5 w-5"/>
                                        </span>
                                        <span class="text-sm font-semibold text-ink-800">Click to upload your photo</span>
                                        <span class="text-xs text-ink-400">JPG, PNG or WebP &middot; maximum 1 MB</span>
                                    </label>

                                    <div x-show="photoPreview" x-cloak class="flex-none">
                                        <img :src="photoPreview" alt="Photo preview"
                                             class="h-40 w-32 rounded-xl border border-ink-900/10 object-cover">
                                        <p class="mt-2 text-center text-xs text-ink-400">Preview</p>
                                    </div>
                                </div>

                                <p class="field-error" x-show="errors.photo" x-text="errors.photo" x-cloak></p>
                                @error('photo')<p class="field-error">{{ $message }}</p>@enderror
                                <p class="field-hint">Used on your reunion identity card. Optional, but recommended.</p>
                            </div>
                        </div>

                        {{-- ---------- Step 6 · Payment ---------- --}}
                        <div x-show="step === 7" x-cloak>
                            @include('partials.register.heading', ['n' => 7, 'en' => 'Payment & Verification', 'bn' => 'পেমেন্ট ও ভেরিফিকেশন'])

                            {{-- Amount due --}}
                            <div class="rounded-2xl bg-ink-900 p-6 text-parchment">
                                <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-400">Total payable</p>
                                <p class="heading-display mt-2 text-4xl">
                                    BDT <span x-text="formattedFee"></span>
                                </p>
                                <div class="mt-4 space-y-1.5 text-sm text-ink-300">
                                    <p class="flex justify-between">
                                        <span x-text="category?.label ?? 'Registration'"></span>
                                        <span>BDT <span x-text="money(categoryFee)"></span></span>
                                    </p>
                                    <p class="flex justify-between" x-show="guestTotal > 0">
                                        <span><span x-text="guestTotal"></span> guest(s) &times; BDT <span x-text="money(guestFee)"></span></span>
                                        <span>BDT <span x-text="money(guestTotal * guestFee)"></span></span>
                                    </p>
                                </div>
                            </div>

                            {{-- Where to send it --}}
                            @php
                                // A number still carrying its placeholder must never be presented
                                // as payable — money sent there would be lost.
                                $unconfigured = fn (string $n) => str_contains($n, 'X') || str_contains($n, '0000 0000');
                                $anyUnconfigured = collect($methods)->contains(fn ($m) => $unconfigured($m['number']));
                            @endphp

                            <div class="mt-8">
                                <p class="field-label">Step 1 &mdash; send the amount to any one of these</p>

                                @if ($anyUnconfigured)
                                    <x-alert type="error" title="Some payment accounts are not set up yet" class="mb-4">
                                        The accounts marked below have not been configured. Please contact the
                                        helpdesk on {{ config('rcmaa.contact.hotline') }} before sending any money.
                                    </x-alert>
                                @endif

                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach ($methods as $key => $method)
                                        @php $pending = $unconfigured($method['number']); @endphp
                                        <div @class([
                                            'rounded-xl border p-4',
                                            'border-red-300 bg-red-50' => $pending,
                                            'border-ink-900/10 bg-white' => ! $pending,
                                        ])>
                                            <div class="flex items-center gap-2.5">
                                                <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $method['colour'] }}"></span>
                                                <span class="text-sm font-semibold text-ink-900">{{ $method['label'] }}</span>
                                                <span class="ml-auto font-mono text-[0.6rem] uppercase tracking-[0.12em] text-ink-400">
                                                    {{ $method['type'] }}
                                                </span>
                                            </div>

                                            @if ($pending)
                                                <p class="mt-2 text-[0.8rem] font-semibold text-red-700">
                                                    Not configured — do not send money to this method.
                                                </p>
                                            @else
                                                <p class="mt-2 font-mono text-[0.9rem] break-all text-ink-700">{{ $method['number'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                {{-- The association's bKash is a Merchant account, so the
                                     customer journey is "Payment" — "Send Money" is for
                                     personal numbers and will not reach it correctly. --}}
                                <p class="field-hint">
                                    This is a bKash <strong>Merchant</strong> account &mdash; choose
                                    <strong>&ldquo;Payment&rdquo;</strong> in your bKash app, not
                                    &ldquo;Send Money&rdquo;.
                                    <span lang="bn" class="font-bangla">বিকাশ অ্যাপে &ldquo;Payment&rdquo; অপশন
                                    ব্যবহার করুন, &ldquo;Send Money&rdquo; নয়।</span>
                                </p>

                                {{-- Donations above the registration fee, at the
                                     association's instruction. It has to be read
                                     BEFORE paying, so it sits with the account
                                     details rather than further down the step. --}}
                                @php
                                    // The number is shown in Bangla numerals, but a tel: link
                                    // needs ASCII — \D would strip Bangla digits entirely.
                                    $donationLine = strtr(config('rcmaa.contact.donation'), [
                                        '০' => '0', '১' => '1', '২' => '2', '৩' => '3', '৪' => '4',
                                        '৫' => '5', '৬' => '6', '৭' => '7', '৮' => '8', '৯' => '9',
                                        '-' => '', ' ' => '',
                                    ]);
                                @endphp
                                <div class="mt-4 rounded-xl border border-brass-500/40 bg-brass-100/60 p-4">
                                    <p lang="bn" class="font-bangla text-[0.86rem] font-semibold text-ink-900">
                                        বিশেষ নির্দেশনা
                                    </p>
                                    <p lang="bn" class="mt-1.5 font-bangla text-[0.82rem] leading-relaxed text-ink-700">
                                        যদি কোনো নিবন্ধনকারী নির্ধারিত রেজিস্ট্রেশন ফি-এর অতিরিক্ত আর্থিক সহযোগিতা
                                        (Donation/Contribution) প্রদান করতে ইচ্ছুক হন, তাহলে অনুগ্রহ করে সরাসরি
                                        পেমেন্ট করার পূর্বে নিচের নম্বরে যোগাযোগ করুন।
                                    </p>
                                    <a href="tel:{{ $donationLine }}"
                                       class="mt-3 inline-flex min-h-11 items-center gap-2 rounded-lg bg-white px-4 py-2.5 font-mono text-[0.9rem] font-semibold text-ink-900 transition hover:text-brass-700">
                                        <x-icon name="phone" class="h-4 w-4 text-brass-600"/>
                                        {{ config('rcmaa.contact.donation') }}
                                    </a>
                                    <p lang="bn" class="mt-2.5 font-bangla text-[0.8rem] leading-relaxed text-ink-600">
                                        অতিরিক্ত অনুদান সংক্রান্ত প্রয়োজনীয় নির্দেশনা ও পেমেন্ট প্রক্রিয়া
                                        যোগাযোগের মাধ্যমে জানিয়ে দেওয়া হবে।
                                    </p>
                                    <p class="mt-2 text-[0.78rem] leading-relaxed text-ink-500">
                                        Registering only? Ignore this and pay the total shown above.
                                    </p>
                                </div>
                            </div>

                            {{-- Proof of payment --}}
                            <div class="mt-8 grid gap-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <p class="field-label">Step 2 &mdash; tell us about the payment you made</p>
                                </div>

                                <x-choice-group name="payment_method" label="Which method did you use?" required
                                                :options="collect($methods)->map(fn ($m) => $m['label'])->all()"
                                                cols="sm:grid-cols-2" class="sm:col-span-2"/>

                                <x-field name="transaction_id" label="Transaction ID (TrxID)" required
                                         placeholder="e.g. 9F2K4L8MZQ"
                                         hint="Copy it exactly from the confirmation SMS."/>
                                <x-field name="sender_number" label="Number / account you sent from" type="tel" required
                                         placeholder="01712345678"/>
                                <x-field name="amount_paid" label="Total Paid Amount (BDT)" type="number" required
                                         class="sm:col-span-2"
                                         placeholder="Enter the exact amount you sent"/>

                                {{-- Receipt. Optional, because somebody who has already
                                     deleted the SMS must still be able to register. --}}
                                <div class="sm:col-span-2">
                                    <label for="field-payment-receipt" class="field-label">
                                        Payment receipt
                                        <span lang="bn" class="field-label-bn"> &middot; পেমেন্টের রসিদ</span>
                                    </label>

                                    <label for="field-payment-receipt"
                                           class="group mt-1.5 flex cursor-pointer items-center gap-4 rounded-2xl border-2 border-dashed border-ink-900/15 bg-white px-5 py-5 transition hover:border-brass-500 hover:bg-brass-100">
                                        <input id="field-payment-receipt" type="file" name="payment_receipt"
                                               accept="image/jpeg,image/png,image/webp,application/pdf"
                                               class="sr-only" @change="handleReceipt($event)">
                                        <span class="grid h-11 w-11 flex-none place-items-center rounded-xl bg-brass-100 text-brass-700 transition group-hover:bg-brass-500 group-hover:text-ink-950">
                                            <x-icon name="upload" class="h-5 w-5"/>
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold text-ink-800"
                                                  x-text="receiptName || 'Attach your bKash confirmation'"></span>
                                            <span class="block text-xs text-ink-400">
                                                Screenshot of your bKash confirmation SMS &middot;
                                                JPG, PNG, WebP or PDF &middot; maximum 4 MB
                                            </span>
                                        </span>
                                    </label>

                                    <div x-show="receiptPreview" x-cloak class="mt-3">
                                        <img :src="receiptPreview" alt="Receipt preview"
                                             class="max-h-56 rounded-xl border border-ink-900/10 object-contain">
                                    </div>

                                    <p class="field-error" x-show="errors.payment_receipt" x-text="errors.payment_receipt" x-cloak></p>
                                    @error('payment_receipt')<p class="field-error">{{ $message }}</p>@enderror
                                    <p class="field-hint">
                                        Optional, but it lets the committee confirm your payment without
                                        telephoning you if the transaction ID does not match.
                                    </p>
                                </div>
                            </div>

                            {{-- Confirmation --}}
                            <label class="choice mt-8 !items-start !py-4">
                                <input type="checkbox" name="terms" value="1" x-model="form.terms" @checked(old('terms'))>
                                <span class="choice-box mt-0.5" aria-hidden="true"></span>
                                <span class="text-[0.82rem] font-normal leading-relaxed">
                                    I confirm that the information provided above is correct, and I have read the
                                    <a href="{{ route('terms') }}" class="underline underline-offset-2 hover:text-brass-700">Terms of Service</a>
                                    and <a href="{{ route('privacy') }}" class="underline underline-offset-2 hover:text-brass-700">Privacy Policy</a>.
                                </span>
                            </label>
                            <p class="field-error" x-show="errors.terms" x-text="errors.terms" x-cloak></p>
                            @error('terms')<p class="field-error">{{ $message }}</p>@enderror
                        </div>

                        {{-- ---------- Navigation (tablet and desktop) ----------
                             On phones this is replaced by the fixed bar below, so
                             Continue is always in reach. --}}
                        <div class="mt-10 hidden items-center justify-between gap-4 border-t border-ink-900/8 pt-7 md:flex">
                            <button type="button" @click="previous()" x-show="step > 1"
                                    class="btn btn-outline btn-sm">
                                <x-icon name="chevron-left" class="h-3.5 w-3.5"/>
                                Back
                            </button>
                            <span x-show="step === 2"></span>

                            <button type="button" @click="next()" x-show="step < totalSteps" class="btn btn-ink">
                                Continue
                                <x-icon name="arrow-right" class="h-4 w-4"/>
                            </button>

                            <button type="submit" x-show="step === totalSteps" :disabled="submitting"
                                    class="btn btn-primary">
                                <span x-show="!submitting">Complete Registration</span>
                                <span x-show="submitting" x-cloak>Submitting…</span>
                                <x-icon name="check" class="h-4 w-4"/>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ======================= Aside ======================= --}}
                <aside class="lg:sticky lg:top-28">
                    <div class="card p-6">
                        <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-700">Your total</p>
                        <p class="heading-display mt-2 text-3xl text-ink-950">
                            BDT <span x-text="formattedFee"></span>
                        </p>
                        <div class="rule-brass my-5"></div>

                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-ink-500">Category</dt>
                                <dd class="truncate font-medium text-ink-900" x-text="category?.label ?? '—'"></dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-ink-500">Name</dt>
                                <dd class="truncate font-medium text-ink-900" x-text="form.full_name_en || '—'"></dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-ink-500">Session</dt>
                                <dd class="font-medium text-ink-900" x-text="form.session || '—'"></dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-ink-500">T-shirt</dt>
                                <dd class="font-medium text-ink-900" x-text="form.tshirt_size || '—'"></dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-ink-500">Guests</dt>
                                <dd class="font-medium text-ink-900" x-text="guestTotal"></dd>
                            </div>
                        </dl>

                        <p class="mt-6 rounded-xl bg-brass-100 px-4 py-3 text-[0.75rem] leading-relaxed text-brass-900">
                            Your progress is saved in this browser. You can close the page and pick up where you
                            left off on the same device.
                        </p>
                    </div>

                    <div class="card mt-4 p-6">
                        <p class="font-mono text-[0.62rem] uppercase tracking-[0.2em] text-brass-700">Helpdesk</p>
                        <p lang="bn" class="mt-2 text-sm text-ink-500">যেকোনো জিজ্ঞাসা বা কারিগরি সহায়তার জন্য যোগাযোগ করুন</p>
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.contact.hotline')) }}"
                           class="mt-3 flex items-center gap-2.5 text-base font-medium text-ink-900 transition hover:text-brass-700">
                            <x-icon name="phone" class="h-4 w-4 text-brass-600"/>
                            {{ config('rcmaa.contact.hotline') }}
                        </a>
                        <a href="mailto:{{ config('rcmaa.contact.email') }}"
                           class="mt-2 flex items-center gap-2.5 text-sm text-ink-600 transition hover:text-brass-700">
                            <x-icon name="mail" class="h-4 w-4 text-brass-600"/>
                            <span class="truncate">{{ config('rcmaa.contact.email') }}</span>
                        </a>
                        <p class="mt-3 text-xs text-ink-400">{{ config('rcmaa.contact.hotline_hours') }}</p>
                    </div>
                </aside>

                {{-- ---------- Mobile action bar ----------
                     Most people register on a phone, and the steps run to two or
                     three screens each. With the buttons at the foot of the card,
                     every step ended in a long scroll to find "Continue" — and the
                     running total sat below the whole form, so nobody saw what they
                     were about to pay while filling it in. Both live here instead,
                     pinned above the thumb. --}}
                <div class="fixed inset-x-0 bottom-0 z-40 border-t border-ink-900/10 bg-white/95 backdrop-blur-sm md:hidden"
                     style="padding-bottom: env(safe-area-inset-bottom)">
                    <div class="flex items-center gap-3 px-4 py-3">
                        <button type="button" @click="previous()" x-show="step > 1"
                                class="grid h-12 w-12 flex-none place-items-center rounded-xl border border-ink-900/15 text-ink-700"
                                aria-label="Back to the previous step">
                            <x-icon name="chevron-left" class="h-5 w-5"/>
                        </button>

                        <div class="min-w-0 flex-1 leading-tight">
                            <p class="font-mono text-[0.55rem] uppercase tracking-[0.16em] text-ink-400">
                                Step <span x-text="step"></span> of <span x-text="totalSteps"></span>
                            </p>
                            {{-- Before a category is picked the fee is genuinely
                                 unknown; "BDT 0" would read as free. --}}
                            <p class="truncate text-[0.95rem] font-semibold text-ink-950"
                               x-text="form.category ? 'BDT ' + formattedFee : 'Choose your category'"></p>
                        </div>

                        <button type="button" @click="next()" x-show="step < totalSteps"
                                class="btn btn-ink !h-12 flex-none !px-5">
                            Continue
                            <x-icon name="arrow-right" class="h-4 w-4"/>
                        </button>

                        <button type="submit" x-show="step === totalSteps" :disabled="submitting"
                                class="btn btn-primary !h-12 flex-none !px-5">
                            <span x-show="!submitting">Complete</span>
                            <span x-show="submitting" x-cloak>Sending…</span>
                            <x-icon name="check" class="h-4 w-4"/>
                        </button>
                    </div>
                </div>

                {{-- Clearance so the bar never covers the last field. --}}
                <div class="h-24 md:hidden" aria-hidden="true"></div>
            </form>
            @endunless
        </div>
    </section>
</x-layout>
