<x-mail::message>
# Registration received

Dear {{ $registration->full_name_en }},

Thank you for registering for the **{{ config('rcmaa.registration.event_name') }}**. We have your details and your payment is now queued for verification by the committee.

<x-mail::panel>
**Reference number:** {{ $registration->reference }}
**Session:** {{ $registration->session }} &middot; {{ $registration->degree_label }}
**Guests:** {{ $registration->guest_total }}
**Amount paid:** BDT {{ number_format($registration->amount_paid) }} ({{ $registration->payment_method_label }} &middot; TrxID {{ $registration->transaction_id }})
**Status:** Awaiting verification
</x-mail::panel>

Please keep your reference number safe — you will need it at the registration desk on the day, and you can use it to check your status online at any time.

<x-mail::button :url="route('registration.status')">
Check your status
</x-mail::button>

Verification usually takes one to two working days. If anything looks wrong, reply to this email or call the helpdesk on {{ config('rcmaa.contact.hotline') }} ({{ config('rcmaa.contact.hotline_hours') }}).

We look forward to seeing you on {{ \Carbon\Carbon::parse(config('rcmaa.registration.event_date'))->format('j F Y') }}.

Warm regards,
**{{ config('rcmaa.name') }}**
</x-mail::message>
