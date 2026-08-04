<x-mail::message>
# Your registration

Dear {{ $registration->full_name_en }},

Use the button below to open your registration for the
**{{ config('rcmaa.registration.event_name') }}**. You can correct your contact
details, change your T-shirt size, print your entry pass, and choose whether you
appear in the public alumni directory.

<x-mail::button :url="$url">
Open my registration
</x-mail::button>

This link works for **{{ $minutes }} minutes** and only from this email address.
There is no password to remember — request a new link whenever you need one.

<x-mail::panel>
**Reference:** {{ $registration->reference }}
**Category:** {{ $registration->category_label }}
**Status:** {{ ucfirst($registration->payment_status) }}
</x-mail::panel>

If you did not ask for this link you can ignore it — nothing has changed on your
registration, and the link cannot be used by anyone who does not have this email.

Warm regards,
**{{ config('rcmaa.name') }}**
</x-mail::message>
