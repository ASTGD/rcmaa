@php
    $opt = config('rcmaa.options');
    $methods = config('rcmaa.payment.methods');

    // Small helper so this long form stays readable.
    $val = fn (string $field, $default = null) => old($field, $registration->{$field} ?? $default);

    // A record imported from the old site may carry a session outside the
    // canonical list. Keep it selectable so an admin can still save the form.
    $sessionChoices = $opt['sessions'];
    if ($registration->session && ! isset($sessionChoices[$registration->session])) {
        $sessionChoices = [$registration->session => $registration->session.' (as recorded)'] + $sessionChoices;
    }

    $mastersSessionChoices = $opt['sessions'];
    if ($registration->masters_session && ! isset($mastersSessionChoices[$registration->masters_session])) {
        $mastersSessionChoices = [$registration->masters_session => $registration->masters_session.' (as recorded)'] + $mastersSessionChoices;
    }
@endphp

<x-admin-layout :title="$title">
    <x-slot:actions>
        <a href="{{ route('admin.registrations.show', $registration) }}" class="btn btn-outline btn-sm">
            <x-icon name="chevron-left" class="h-3.5 w-3.5"/>Cancel
        </a>
    </x-slot:actions>

    <x-alert type="info" title="Correcting on the registrant's behalf" class="mb-6">
        Changing the category re-derives the amount due. Every change is appended to the
        admin note with your name and the time, so there is a record of what was altered.
    </x-alert>

    <form method="POST" action="{{ route('admin.registrations.update-details', $registration) }}"
          x-data="{ degree: '{{ $val('degree') }}' }"
          class="grid max-w-5xl gap-6 lg:grid-cols-[1fr_18rem] lg:items-start">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            {{-- Category --}}
            <div class="card p-6">
                <h2 class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">Category</h2>
                <div class="mt-4 grid gap-2.5 sm:grid-cols-2">
                    @foreach ($categories as $key => $cat)
                        <label class="choice">
                            <input type="radio" name="category" value="{{ $key }}"
                                   @checked($val('category') === $key)>
                            <span class="choice-box" aria-hidden="true"></span>
                            <span class="min-w-0">
                                <span class="block truncate">{{ $cat['label'] }}</span>
                                <span class="block text-[0.7rem] font-normal text-ink-400">
                                    &#2547;{{ number_format($cat['fee']) }}{{ $cat['allows_guests'] ? ' · guests allowed' : '' }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            @foreach ([
                'Personal' => [
                    ['full_name_en', 'Full name (English)', 'text', true],
                    ['full_name_bn', 'Full name (Bangla)', 'text', false],
                    ['mobile', 'Mobile', 'tel', true],
                    ['whatsapp', 'WhatsApp', 'tel', false],
                    ['email', 'Email', 'email', true],
                ],
                'Academic' => [
                    ['passing_year', 'Passing year', 'number', false],
                    ['class_roll', 'Class roll', 'text', false],
                    ['registration_no', 'Registration no', 'text', false],
                ],
                'Professional' => [
                    ['profession', 'Profession / sector', 'text', false],
                    ['designation', 'Designation', 'text', false],
                    ['organization', 'Organization', 'text', false],
                ],
                'Payment' => [
                    ['transaction_id', 'Transaction ID', 'text', true],
                    ['sender_number', 'Sent from', 'tel', true],
                    ['amount_paid', 'Amount paid (BDT)', 'number', true],
                ],
            ] as $section => $fields)
                <div class="card p-6">
                    <h2 class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">{{ $section }}</h2>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        @foreach ($fields as [$name, $label, $type, $required])
                            <div>
                                <label for="f-{{ $name }}" class="field-label">
                                    {{ $label }}@if ($required)<span class="text-red-600">*</span>@endif
                                </label>
                                <input id="f-{{ $name }}" name="{{ $name }}" type="{{ $type }}"
                                       class="input @if (! empty($registration->{$name}) && $name === 'full_name_bn') font-bangla @endif"
                                       value="{{ $val($name) }}" @if ($errors->has($name)) aria-invalid="true" @endif>
                                @error($name)<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Selects, addresses and the long text --}}
            <div class="card space-y-5 p-6">
                <h2 class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">Details</h2>

                <div class="grid gap-5 sm:grid-cols-3">
                    @foreach ([
                        ['blood_group', 'Blood group', array_combine($opt['blood_groups'], $opt['blood_groups']), false],
                        ['session', 'Session', $sessionChoices, true],
                        ['degree', 'Degree', $opt['degrees'], true],
                        ['tshirt_size', 'T-shirt size', array_combine($opt['tshirt_sizes'], $opt['tshirt_sizes']), true],
                        ['employment_status', 'Employment', $opt['employment_statuses'], true],
                        ['payment_method', 'Payment method', collect($methods)->map(fn ($m) => $m['label'])->all(), true],
                        ['guest_count', 'Accompanying Guests / Spouse · অতিথি সংখ্যা', $opt['guest_counts'], true],
                    ] as [$name, $label, $choices, $required])
                        <div>
                            <label for="s-{{ $name }}" class="field-label">
                                <span @if ($name === 'session') x-text="{ bsc: 'Honours Session', msc: 'Masters Session', both: 'Honours Session', previous_masters: 'Previous Master\'s Session' }[degree] || 'Session'" @endif>{{ $label }}</span>@if ($required)<span class="text-red-600">*</span>@endif
                            </label>
                            <select id="s-{{ $name }}" name="{{ $name }}" class="input"
                                    @if ($name === 'degree') x-model="degree" @endif>
                                @unless ($required)<option value="">—</option>@endunless
                                @foreach ($choices as $k => $text)
                                    @php $v = is_int($k) ? $text : $k; @endphp
                                    <option value="{{ $v }}" @selected($val($name) === $v)>{{ $text }}</option>
                                @endforeach
                            </select>
                            @error($name)<p class="field-error">{{ $message }}</p>@enderror
                        </div>

                        @if ($name === 'degree')
                            <div x-show="degree === 'both'" x-cloak>
                                <label for="s-masters_session" class="field-label">
                                    Masters Session<span class="text-red-600">*</span>
                                </label>
                                <select id="s-masters_session" name="masters_session" class="input">
                                    <option value="">—</option>
                                    @foreach ($mastersSessionChoices as $k => $text)
                                        @php $v = is_int($k) ? $text : $k; @endphp
                                        <option value="{{ $v }}" @selected($val('masters_session') === $v)>{{ $text }}</option>
                                    @endforeach
                                </select>
                                @error('masters_session')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        @endif
                    @endforeach

                    <div>
                        <label class="field-label">Cultural programme</label>
                        <select name="cultural_program" class="input">
                            <option value="1" @selected($val('cultural_program'))>Yes — performing</option>
                            <option value="0" @selected(! $val('cultural_program'))>No</option>
                        </select>
                    </div>
                </div>

                @foreach ([['present_address', 'Present address', true], ['permanent_address', 'Permanent address', false], ['memories', 'Memories & remarks', false]] as [$name, $label, $required])
                    <div>
                        <label for="t-{{ $name }}" class="field-label">
                            {{ $label }}@if ($required)<span class="text-red-600">*</span>@endif
                        </label>
                        <textarea id="t-{{ $name }}" name="{{ $name }}" rows="{{ $name === 'memories' ? 5 : 2 }}"
                                  class="input">{{ $val($name) }}</textarea>
                        @error($name)<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Aside --}}
        <aside class="space-y-4 lg:sticky lg:top-24">
            <div class="card p-5">
                <h2 class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">Directory</h2>
                <p class="mt-2 text-xs leading-relaxed text-ink-500">
                    The Privacy Policy lets a registrant ask to be taken out of the public
                    directory. Unticking this honours that without touching their seat.
                </p>
                <label class="choice mt-4 !py-2.5 !text-[0.8rem]">
                    <input type="checkbox" name="listed_in_directory" value="1"
                           @checked(old('listed_in_directory', $registration->listed_in_directory))>
                    <span class="choice-box" aria-hidden="true"></span>
                    <span>List in the members-only alumni directory</span>
                </label>
            </div>

            <div class="card p-5">
                <p class="font-mono text-[0.62rem] uppercase tracking-[0.16em] text-ink-500">Reference</p>
                <p class="heading-display mt-1 text-xl text-ink-950">{{ $registration->reference }}</p>
                <p class="mt-3 text-xs text-ink-400">
                    Guests: {{ $registration->guest_total }} — edit guests by asking the registrant
                    to contact the helpdesk, or delete and re-register.
                </p>
                <button type="submit" class="btn btn-primary mt-5 w-full">Save Changes</button>
            </div>
        </aside>
    </form>
</x-admin-layout>
