<x-mail::message>
# Registration verified

Dear {{ $registration->full_name_en }},

Great news! Your payment and registration for the **{{ config('rcmaa.registration.event_name') }}** have been verified by the committee.

Your registration is now fully confirmed.

<x-mail::panel>
**Reference number:** {{ $registration->reference }}
**Session:** {{ $registration->session }} &middot; {{ $registration->degree_label }}
**Guests:** {{ $registration->guest_total }}
**Amount paid:** BDT {{ number_format($registration->amount_paid) }} ({{ $registration->payment_method_label }})
**Status:** Confirmed &amp; Verified
</x-mail::panel>

Please save this email or keep your reference number safe. You will need it at the registration desk on the day of the event. You can check your registration card and details online at any time.

<x-mail::button :url="route('registration.status')">
View registration status
</x-mail::button>

If you have any questions or need to make changes, please contact the helpdesk on {{ config('rcmaa.contact.helpline') }} ({{ config('rcmaa.contact.helpline_hours') }}).

We look forward to welcoming you on {{ \Carbon\Carbon::parse(config('rcmaa.registration.event_date'))->format('j F Y') }}.

Warm regards,
**{{ config('rcmaa.name') }}**
</x-mail::message>
