{{-- NOTE FOR THE CLIENT: baseline terms covering registration and payment.
     Have the committee review and adjust the refund clause to its own policy. --}}
<x-legal-page
    title="Terms of Service"
    updated="{{ now()->format('j F Y') }}"
    intro="The terms on which the association provides this website and accepts event registrations."
    :sections="[
        'About these terms' => [
            'This website is operated by the Rajshahi College Mathematics Alumni Association. By registering for membership or an event through this site, you agree to the terms set out below.',
        ],
        'Eligibility' => [
            'Membership is open to graduates of the Department of Mathematics, Rajshahi College. The committee reserves the right to verify any claim of graduation and to decline or cancel a registration where it cannot be substantiated.',
        ],
        'Accurate information' => [
            'You are responsible for the accuracy of the information you submit. Registrations containing false details may be cancelled without refund. If you notice a mistake after submitting, contact the helpdesk with your reference number rather than submitting a second registration.',
        ],
        'Registration and payment' => [
            'A registration is provisional until the committee has verified the payment against the receiving account. Verification is carried out manually and normally takes one to two working days.',
            'Each transaction reference may be used for one registration only. Submitting a transaction reference that has already been used, or that cannot be matched to a received payment, will result in the registration being rejected.',
            'Registration fees and guest fees are those displayed on the registration form at the time you register.',
        ],
        'Cancellations and refunds' => [
            'Requests to cancel a registration and obtain a refund should be made through the helpdesk before the published registration deadline. After the deadline, refunds are at the committee\'s discretion, because catering, merchandise and venue arrangements are committed against confirmed numbers.',
        ],
        'Conduct at events' => [
            'Attendees are expected to conduct themselves in a manner befitting the institution. The committee may refuse admission to, or remove from an event, anyone whose conduct endangers or seriously disrupts others.',
            'Prior registration is mandatory in order to perform in the cultural programme. Performers cannot be added at the venue on the day.',
        ],
        'Photography at events' => [
            'Photographs and video are taken at association events and may be published on this website and on the association\'s social media. If you would prefer not to appear, tell a member of the committee at the event and we will honour that.',
        ],
        'Content on this site' => [
            'The text, photographs and design of this site belong to the association or are used with permission. Please ask before reproducing them elsewhere.',
        ],
        'Changes' => [
            'These terms may be updated from time to time. The date at the top of this page shows when they were last changed.',
        ],
    ]"/>
