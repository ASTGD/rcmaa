{{-- NOTE FOR THE CLIENT: this is a plain-language policy describing what the
     site actually does with data. Have it reviewed before launch if the
     association needs it to meet a specific legal standard. --}}
<x-legal-page
    title="Privacy Policy"
    updated="{{ now()->format('j F Y') }}"
    intro="What we collect when you register, why we hold it, and who can see it."
    :sections="[
        'What we collect' => [
            'When you register for membership or the Grand Reunion we collect the information you enter on the registration form: your name in English and Bangla, blood group, mobile and WhatsApp numbers, email address, present and permanent addresses, academic record, employment details, T-shirt size, guest details, any memories you choose to share, your photograph, and your payment details (method, transaction ID, sending number and amount).',
            'We also record the IP address the registration was submitted from, purely to help us investigate abuse.',
        ],
        'Why we hold it' => [
            'Your information is used to administer your membership and your place at association events. Specifically:',
            [
                'To verify that your registration payment was received.',
                'To produce your reunion identity card, T-shirt and catering arrangements.',
                'To contact you about your registration and about association business.',
                'To maintain the alumni directory.',
            ],
        ],
        'What is published' => [
            'The public alumni directory shows your name, session, passing year, profession, photograph and mobile number. The mobile number is published so that alumni can reach one another directly — it is visible to anyone who visits the directory, including people outside the association.',
            'Your WhatsApp number, email address, present and permanent addresses, blood group and payment details are not published. They are held by the committee for administering your registration.',
            'If you would prefer your mobile number not to appear, write to us at the address below and we will remove it from the directory.',
            'Committee members with administrator access can see your full record in order to verify payments and organise events.',
        ],
        'Payment information' => [
            'We do not process card payments and we never ask for a PIN, OTP or password. You send money directly to the association through bKash, Nagad, Rocket or a bank transfer, and then tell us the transaction reference so a committee member can match it against the account. We store only that reference, the sending number and the amount.',
        ],
        'How long we keep it' => [
            'Registration records are kept for as long as you remain a member of the association, and are used to maintain the historical record of who attended which reunion. You may ask us to remove your entry from the public directory, or to delete your record entirely, at any time.',
        ],
        'Cookies' => [
            'This site sets a session cookie that keeps you signed in and protects forms against cross-site request forgery. It sets no advertising or third-party tracking cookies.',
            'Your progress through the registration form is saved in your own browser\'s local storage so you can leave and come back. That data never leaves your device until you submit the form, and it is cleared once you do.',
        ],
        'Your rights' => [
            'You can ask us what we hold about you, ask for it to be corrected, ask for it to be removed from the public directory, or ask for it to be deleted altogether. Write to the address below and we will act on it.',
        ],
    ]"/>
