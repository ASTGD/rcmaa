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
    <div x-data="{ editing: {{ $errors->any() ? 'true' : 'false' }} }">
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
            <div class="mt-7 flex flex-wrap items-center gap-3" data-reveal data-reveal-delay="0.3">
                <a href="{{ route('member.slip.registration') }}" class="btn btn-outline-light">
                    <x-icon name="download" class="h-4 w-4"/>Registration slip
                </a>
                <a href="{{ route('member.slip.payment') }}" class="btn btn-outline-light">
                    <x-icon name="download" class="h-4 w-4"/>Payment slip
                </a>
                <a href="{{ route('member.pass') }}" class="btn btn-outline-light">
                    <x-icon name="download" class="h-4 w-4"/>Entry pass
                </a>
                <form method="POST" action="{{ route('member.logout') }}">
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

                {{-- Read-only Profile Overview --}}
                <div x-show="!editing" class="space-y-6">
                    {{-- Header Profile Card --}}
                    <div class="card p-6 md:p-8 flex flex-col sm:flex-row gap-6 justify-between items-center sm:items-start">
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 flex-1 min-w-0">
                            {{-- Profile Photo --}}
                            <div class="h-28 w-28 flex-none overflow-hidden rounded-2xl border-2 border-brass-500/20 bg-ink-900 shadow-sm">
                                @if ($r->photo_url)
                                    <img src="{{ $r->photo_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <div class="grid h-full place-items-center text-brass-500 bg-ink-900">
                                        <span class="heading-display text-4xl">
                                            {{ mb_strtoupper(mb_substr(preg_replace('/^(Md\.|Mst\.|Mrs\.|Mr\.|Dr\.)\s*/i', '', $r->full_name_en), 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Name & Meta --}}
                            <div class="text-center sm:text-left min-w-0">
                                <h1 class="heading-display text-2xl md:text-3xl text-ink-950 truncate">{{ $r->full_name_en }}</h1>
                                <span class="inline-flex mt-2 rounded-full bg-brass-100 px-3 py-1 text-xs font-semibold text-brass-800">
                                    {{ $r->category_label }} Member
                                </span>
                                
                                <div class="mt-4 space-y-2 text-sm text-ink-600">
                                    <p class="flex items-center justify-center sm:justify-start gap-2">
                                        <x-icon name="user" class="h-4 w-4 text-brass-600"/>
                                        <span>Member ID: <strong>{{ $r->reference }}</strong></span>
                                    </p>
                                    <p class="flex items-center justify-center sm:justify-start gap-2">
                                        <x-icon name="calendar" class="h-4 w-4 text-brass-600"/>
                                        <span>Registered on: {{ $r->created_at->format('j F Y') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Edit Button --}}
                        <button type="button" @click="editing = true" x-show="!editing" class="btn btn-outline self-center sm:self-start flex-none">
                            <x-icon name="edit" class="h-4 w-4"/>Edit Profile
                        </button>
                    </div>

                    {{-- Personal & Participation Information --}}
                    <div class="card p-6 md:p-8">
                        <h2 class="flex items-center gap-2.5 heading-display text-lg text-ink-950 pb-4 border-b border-ink-900/6">
                            <x-icon name="user" class="h-5 w-5 text-brass-600"/>
                            Personal & Participation Information
                        </h2>

                        <div class="mt-6 grid gap-x-8 gap-y-4 sm:grid-cols-2 text-sm">
                            {{-- Left Column --}}
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="user" class="h-4 w-4 text-brass-600/70"/>
                                        Full Name
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->full_name_en }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="tag" class="h-4 w-4 text-brass-600/70"/>
                                        Category
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->category_label }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="tag" class="h-4 w-4 text-brass-600/70"/>
                                        Degree
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->degree_label }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="clock" class="h-4 w-4 text-brass-600/70"/>
                                        Session / Batch
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->session }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="clock" class="h-4 w-4 text-brass-600/70"/>
                                        Masters Session
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->masters_session ?: '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="chevron-down" class="h-4 w-4 text-brass-600/70"/>
                                        T-Shirt Size
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->tshirt_size }}</span>
                                </div>
                            </div>

                            {{-- Right Column --}}
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="users" class="h-4 w-4 text-brass-600/70"/>
                                        Accompanying Guests
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->guest_total }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="heart" class="h-4 w-4 text-brass-600/70"/>
                                        Blood Group
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->blood_group ?: '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="music" class="h-4 w-4 text-brass-600/70"/>
                                        Cultural Programme
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->cultural_program ? 'Participating' : 'Not Participating' }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="mail" class="h-4 w-4 text-brass-600/70"/>
                                        Email
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right truncate max-w-[200px]" title="{{ $r->email }}">{{ $r->email }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="phone" class="h-4 w-4 text-brass-600/70"/>
                                        Phone
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right">{{ $r->mobile }}</span>
                                </div>
                                <div class="flex items-center justify-between border-b border-ink-900/5 pb-2">
                                    <span class="flex items-center gap-2 text-ink-500 font-medium">
                                        <x-icon name="map-pin" class="h-4 w-4 text-brass-600/70"/>
                                        Address
                                    </span>
                                    <span class="font-semibold text-ink-900 text-right truncate max-w-[200px]" title="{{ $r->present_address }}">{{ $r->present_address }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Event Information --}}
                    <div class="card p-6 md:p-8">
                        <h2 class="flex items-center gap-2.5 heading-display text-lg text-ink-950 pb-4 border-b border-ink-900/6">
                            <x-icon name="calendar" class="h-5 w-5 text-brass-600"/>
                            Event Information
                        </h2>

                        <div class="mt-6 grid gap-6 sm:grid-cols-3">
                            <div class="flex items-start gap-4">
                                <span class="grid h-12 w-12 flex-none place-items-center rounded-xl bg-ink-950 text-brass-500">
                                    <x-icon name="calendar" class="h-5 w-5"/>
                                </span>
                                <div>
                                    <p class="text-xs text-ink-500 font-medium">Event Name</p>
                                    <p class="mt-1 text-sm font-bold text-ink-900">Math Nexus 2026</p>
                                    <p class="text-[0.7rem] text-ink-400">Reunion & Cultural Fest</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <span class="grid h-12 w-12 flex-none place-items-center rounded-xl bg-ink-950 text-brass-500">
                                    <x-icon name="clock" class="h-5 w-5"/>
                                </span>
                                <div>
                                    <p class="text-xs text-ink-500 font-medium">Event Date</p>
                                    <p class="mt-1 text-sm font-bold text-ink-900">Saturday</p>
                                    <p class="text-[0.7rem] text-ink-400">19 December 2026</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <span class="grid h-12 w-12 flex-none place-items-center rounded-xl bg-ink-950 text-brass-500">
                                    <x-icon name="map-pin" class="h-5 w-5"/>
                                </span>
                                <div>
                                    <p class="text-xs text-ink-500 font-medium">Venue</p>
                                    <p class="mt-1 text-sm font-bold text-ink-900">Rajshahi College Campus</p>
                                    <p class="text-[0.7rem] text-ink-400">Rajshahi, Bangladesh</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Banner --}}
                    <div class="flex items-center gap-3.5 rounded-2xl bg-amber-50 border border-amber-500/25 p-4 text-amber-800 text-sm font-medium">
                        <x-icon name="lock" class="h-5 w-5 text-amber-600 flex-none"/>
                        <span>This is your profile overview. Click on &ldquo;Edit Profile&rdquo; button to update your information.</span>
                    </div>
                </div>

                {{-- Edit profile Form --}}
                <form x-show="editing" method="POST" action="{{ route('member.profile.update') }}"
                      enctype="multipart/form-data" class="card p-6 md:p-8" x-cloak>
                    @csrf @method('PATCH')

                    <h2 class="heading-display text-xl text-ink-950">Your details</h2>
                    <p class="prose-rc mt-1.5 text-sm">
                        Change anything here yourself. Your session, degree, category and payment can
                        only be altered by the committee — call the helpdesk on {{ config('rcmaa.contact.helpline') }}.
                    </p>

                    <fieldset :disabled="!editing" class="contents">
                        {{-- Profile picture. Sits above the fields because it is the one
                             thing here that is not a text box, and burying a file input
                             among them is how people miss it. --}}
                        <div class="mt-7 flex flex-wrap items-center gap-5 rounded-2xl bg-parchment-dim p-5">
                            <span class="grid h-20 w-20 flex-none place-items-center overflow-hidden rounded-2xl bg-ink-900 text-brass-500">
                                @if ($r->photo_url)
                                    <img src="{{ $r->photo_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    <span class="heading-display text-2xl">
                                        {{ mb_strtoupper(mb_substr(preg_replace('/^(Md\.|Mst\.|Mrs\.|Mr\.|Dr\.)\s*/i', '', $r->full_name_en), 0, 1)) }}
                                    </span>
                                @endif
                            </span>

                            <div class="min-w-0 flex-1">
                                <label for="member-photo" class="field-label">
                                    Profile picture <span lang="bn" class="field-label-bn">&middot; ছবি</span>
                                </label>
                                <input id="member-photo" type="file" name="photo"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="mt-2 block w-full text-sm text-ink-600
                                              file:mr-3 file:rounded-lg file:border-0 file:bg-brass-100 file:px-3.5 file:py-2
                                              file:text-sm file:font-medium file:text-brass-800 hover:file:bg-brass-200">
                                @error('photo')<p class="field-error">{{ $message }}</p>@enderror
                                <p class="mt-1.5 text-xs text-ink-400">
                                    JPG, PNG or WebP &middot; maximum {{ round(config('rcmaa.registration.photo_max_kb') / 1024, 1) }} MB.
                                    Shown in the alumni directory if you are listed.
                                </p>
                            </div>
                        </div>

                        <div class="mt-7 grid gap-6 sm:grid-cols-2">
                            <x-field name="full_name_en" autocomplete="name" :value="$r->full_name_en" label="Full name (English)" required :model="false"/>
                            <x-field name="full_name_bn" :value="$r->full_name_bn" label="Full name" bn="বাংলায় নাম" :model="false"/>
                            <x-field name="mobile" autocomplete="tel" :value="$r->mobile" label="Mobile" bn="মোবাইল" type="tel" required :model="false"/>
                            <x-field name="whatsapp" autocomplete="tel" :value="$r->whatsapp" label="WhatsApp" type="tel" :model="false"/>
                            <x-field name="linkedin_url" :value="$r->linkedin_url" label="LinkedIn Profile Link" :model="false" placeholder="https://linkedin.com/in/username"/>

                            @if ($r->category === 'teacher')
                                <div class="min-w-0">
                                    <label class="field-label" for="field-teacher-type">
                                        Role / Designation Type <span lang="bn" class="field-label-bn">&middot; ধরণ</span>
                                    </label>
                                    <select id="field-teacher-type" name="teacher_type" class="input mt-2">
                                        <option value="">Select type</option>
                                        @foreach ($opt['teacher_types'] as $key => $label)
                                            <option value="{{ $key }}" @selected(old('teacher_type', $r->teacher_type) === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('teacher_type')<p class="field-error">{{ $message }}</p>@enderror
                                </div>
                            @endif

                            <x-field name="blood_group" :value="$r->blood_group" label="Blood group" type="select" :model="false"
                                     :options="array_combine($opt['blood_groups'], $opt['blood_groups'])"
                                     placeholder="Select blood group"/>
                            <x-field name="tshirt_size" :value="$r->tshirt_size" label="T-shirt size" type="select" required :model="false"
                                     :options="array_combine($opt['tshirt_sizes'], $opt['tshirt_sizes'])"/>
                            <x-field name="present_address" autocomplete="street-address" :value="$r->present_address" label="Present address" bn="বর্তমান ঠিকানা"
                                     type="textarea" rows="3" required :model="false" class="sm:col-span-2"/>
                            <x-field name="permanent_address" :value="$r->permanent_address" label="Permanent address" bn="স্থায়ী ঠিকানা"
                                     type="textarea" rows="3" :model="false" class="sm:col-span-2"/>
                            <x-field name="employment_status" :value="$r->employment_status" label="Employment status" type="select" :model="false"
                                     :options="$opt['employment_statuses']" placeholder="Select your status"
                                     onchange="document.getElementById('work-location-wrapper').style.display = ['employed','self_employed'].includes(this.value) ? 'block' : 'none'"/>
                            <x-field name="profession" :value="$r->profession" label="Profession / sector" :model="false"/>
                            <x-field name="designation" autocomplete="organization-title" :value="$r->designation" label="Designation" bn="পদবী" :model="false"/>
                            <x-field name="organization" autocomplete="organization" :value="$r->organization" label="Organization" bn="কর্মস্থল" :model="false" class="sm:col-span-2"/>

                            <div class="min-w-0 sm:col-span-2" id="work-location-wrapper" style="display: {{ in_array(old('employment_status', $r->employment_status), ['employed', 'self_employed']) ? 'block' : 'none' }}">
                                <label class="field-label" for="field-work-location">
                                    Work Location <span lang="bn" class="field-label-bn">&middot; কর্মস্থলের জেলা</span>
                                </label>
                                <select id="field-work-location" name="work_location" class="input mt-2">
                                    <option value="">Select district</option>
                                    @foreach (array_keys(config('bd-geo')) as $district)
                                        <option value="{{ $district }}" @selected(old('work_location', $r->work_location) === $district)>{{ $district }}</option>
                                    @endforeach
                                </select>
                                @error('work_location')<p class="field-error">{{ $message }}</p>@enderror
                            </div>

                            <x-field name="memories" :value="$r->memories" label="Your memories of the department" type="textarea"
                                     rows="5" :model="false" class="sm:col-span-2"/>
                        </div>

                        <div class="mt-7 rounded-2xl bg-parchment-dim p-5">
                            <label class="choice !items-start !border-0 !bg-transparent !py-0">
                                <input type="checkbox" name="listed_in_directory" value="1"
                                       @checked(old('listed_in_directory', $r->listed_in_directory))>
                                <span class="choice-box mt-0.5" aria-hidden="true"></span>
                                <span class="text-[0.85rem] font-normal leading-relaxed">
                                    <strong class="font-semibold">Show me in the alumni directory.</strong>
                                    It lists your name, session, profession, photograph and mobile number so
                                    fellow graduates can reach you. Your email and addresses are never published.
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="mt-7 flex items-center gap-3">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" @click="editing = false" class="btn btn-outline">Cancel</button>
                    </div>
                </form>
            </div>

            {{-- Payment Details --}}
            <aside class="space-y-4 lg:sticky lg:top-28">
                <div class="card overflow-hidden">
                    <h2 class="border-b border-ink-900/8 bg-parchment-dim px-5 py-3 font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">
                        Payment Status
                    </h2>
                    <dl class="divide-y divide-ink-900/6 text-sm">
                        @php
                            $isVerified = $r->payment_status === 'verified';
                            $due = $isVerified ? 0 : $r->amount_due;
                            $paid = $isVerified ? $r->amount_paid : 0;
                        @endphp
                        @foreach ([
                            'Status' => $isVerified ? 'Paid (পরিশোধিত)' : 'Pending (অপেক্ষমান)',
                            'Amount due' => 'BDT '.number_format($due),
                            'Amount paid' => 'BDT '.number_format($paid),
                        ] as $k => $v)
                            <div class="flex justify-between gap-3 px-5 py-2.5">
                                <dt class="text-ink-500">{{ $k }}</dt>
                                <dd @class([
                                    'text-right font-medium',
                                    'text-emerald-700 font-bold' => $k === 'Status' && $isVerified,
                                    'text-amber-700 font-bold' => $k === 'Status' && ! $isVerified,
                                    'text-ink-900' => $k !== 'Status',
                                ])>{{ $v }}</dd>
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

                        <form method="POST" action="{{ route('member.receipt') }}"
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
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-brass-700">Your account</p>
                    <p class="mt-2 truncate text-sm font-medium text-ink-900">{{ $r->email }}</p>
                    <p class="mt-1 text-xs text-ink-400">
                        @if ($r->hasPassword())
                            You sign in with this address and your password.
                        @else
                            No password set yet — you are signing in by emailed link.
                        @endif
                    </p>
                    <a href="{{ route('member.password.create') }}" class="btn btn-outline btn-sm mt-4 w-full">
                        {{ $r->hasPassword() ? 'Change password' : 'Set a password' }}
                    </a>
                </div>

                <div class="card p-5">
                    <p class="font-mono text-[0.62rem] uppercase tracking-[0.18em] text-brass-700">Helpdesk</p>
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', config('rcmaa.contact.helpline')) }}"
                       class="mt-2 block text-base font-medium text-ink-900 transition hover:text-brass-700">
                        {{ config('rcmaa.contact.helpline') }}
                    </a>
                    <p class="mt-1 text-xs text-ink-400">{{ config('rcmaa.contact.helpline_hours') }}</p>
                </div>
            </aside>
        </div>
    </section>
    </div>
</x-layout>
