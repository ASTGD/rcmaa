{{--
    The registration confirmation slip a member downloads from their dashboard.

    Everything they filled in, on one page. Rendered by dompdf, so see
    pdf/partials/style for what CSS is actually available here.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Registration slip {{ $r->reference }}</title>
    @include('pdf.partials.style')
</head>
<body>
    @include('pdf.partials.head', ['kind' => 'Registration Slip'])

    @php
        $status = match ($r->payment_status) {
            'verified' => ['Verified', 'chip-ok'],
            'rejected' => ['Not verified', 'chip-no'],
            default => ['Awaiting verification', 'chip-wait'],
        };
    @endphp

    <table class="data">
        <tr><td class="k">Name</td><td class="v">{{ $r->full_name_en }}</td></tr>
        @if ($r->full_name_bn)
            <tr><td class="k">Name (Bangla)</td><td class="v">{{ $r->full_name_bn }}</td></tr>
        @endif
        <tr><td class="k">Category</td><td class="v">{{ $r->category_label }}</td></tr>
        <tr><td class="k">Status</td><td class="v"><span class="chip {{ $status[1] }}">{{ $status[0] }}</span></td></tr>
        <tr><td class="k">Registered on</td><td class="v">{{ $r->created_at?->format('j F Y') }}</td></tr>
    </table>

    <h2>Academic</h2>
    <table class="data">
        <tr><td class="k">Session</td><td class="v">{{ $r->session ?: '—' }}</td></tr>
        @if ($r->masters_session)
            <tr><td class="k">Masters session</td><td class="v">{{ $r->masters_session }}</td></tr>
        @endif
        <tr><td class="k">Degree</td><td class="v">{{ $r->degree_label ?: '—' }}</td></tr>
        <tr><td class="k">Passing year</td><td class="v">{{ $r->passing_year ?: '—' }}</td></tr>
        <tr><td class="k">Class roll</td><td class="v">{{ $r->class_roll ?: '—' }}</td></tr>
        <tr><td class="k">Registration no.</td><td class="v">{{ $r->registration_no ?: '—' }}</td></tr>
    </table>

    <h2>Contact</h2>
    <table class="data">
        <tr><td class="k">Mobile</td><td class="v">{{ $r->mobile }}</td></tr>
        @if ($r->whatsapp)
            <tr><td class="k">WhatsApp</td><td class="v">{{ $r->whatsapp }}</td></tr>
        @endif
        <tr><td class="k">Email</td><td class="v">{{ $r->email }}</td></tr>
        @if ($r->blood_group)
            <tr><td class="k">Blood group</td><td class="v">{{ $r->blood_group }}</td></tr>
        @endif
        <tr><td class="k">Present address</td><td class="v">{{ $r->present_address }}</td></tr>
        @if ($r->permanent_address)
            <tr><td class="k">Permanent address</td><td class="v">{{ $r->permanent_address }}</td></tr>
        @endif
    </table>

    @if ($r->profession || $r->designation || $r->organization)
        <h2>Professional</h2>
        <table class="data">
            @if ($r->employment_status)
                <tr><td class="k">Employment status</td>
                    <td class="v">{{ config('rcmaa.options.employment_statuses')[$r->employment_status] ?? $r->employment_status }}</td></tr>
            @endif
            @if ($r->profession)
                <tr><td class="k">Profession</td><td class="v">{{ $r->profession }}</td></tr>
            @endif
            @if ($r->designation)
                <tr><td class="k">Designation</td><td class="v">{{ $r->designation }}</td></tr>
            @endif
            @if ($r->organization)
                <tr><td class="k">Organization</td><td class="v">{{ $r->organization }}</td></tr>
            @endif
        </table>
    @endif

    <h2>Reunion</h2>
    <table class="data">
        <tr><td class="k">T-shirt size</td><td class="v">{{ $r->tshirt_size }}</td></tr>
        <tr><td class="k">Cultural programme</td><td class="v">{{ $r->cultural_program ? 'Taking part' : 'Not taking part' }}</td></tr>
        <tr><td class="k">Guests</td><td class="v">{{ $r->guest_total }}</td></tr>
        <tr><td class="k">Listed in directory</td><td class="v">{{ $r->listed_in_directory ? 'Yes' : 'No' }}</td></tr>
    </table>

    <div class="note">
        Bring this slip, or your reference number <strong>{{ $r->reference }}</strong>, to the
        registration desk on the day. Details can be corrected from your account at any time
        before the reunion.
    </div>

    <div class="foot">
        {{ config('rcmaa.name') }} &middot; Generated {{ now()->format('j F Y, g:ia') }} &middot;
        Helpline {{ config('rcmaa.contact.helpline') }}
    </div>
</body>
</html>
