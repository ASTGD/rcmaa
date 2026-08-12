<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Association identity
    |--------------------------------------------------------------------------
    */

    'name' => 'Rajshahi College Mathematics Alumni Association',
    'short_name' => 'RCMAA',
    'tagline' => 'Reconnecting generations of mathematical minds from Rajshahi College.',

    // Two distinct dates, easily confused: the College has taught since 1873;
    // the association itself was founded in 2026 (its seal reads EST. 2026).
    'college_founded' => 1873,
    'founded' => 2026,

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    |
    | As the association listed them on 4 August 2026. Two numbers now: the
    | Official Contact line, and a separate Registration Helpline. WhatsApp is
    | the Official Contact number for both channels.
    |
    */

    'contact' => [
        'email' => env('RCMAA_EMAIL', 'rcmaa.alumni@gmail.com'),

        // Official Contact — the association's main line, 9am to 9pm.
        'phone' => env('RCMAA_PHONE', '01400-366369'),
        'hotline' => env('RCMAA_HOTLINE', '01400-366369'),
        'hotline_hours' => '09:00 AM — 09:00 PM',

        // Registration Helpline — a different number, same hours.
        'helpline' => env('RCMAA_HELPLINE', '01990168773'),
        'helpline_hours' => '09:00 AM — 09:00 PM',

        // Helpdesk — the association gave hours and an email but no number.
        'helpdesk' => env('RCMAA_HELPDESK', null),
        'helpdesk_hours' => '09:00 AM — 02:00 PM',

        // Donations above the registration fee are arranged by phone first.
        'donation' => env('RCMAA_DONATION_PHONE', '০১৪০০-৩৬৬৩৬৯'),

        'whatsapp_number' => env('RCMAA_WHATSAPP_NUMBER', '01400-366369'),
        'whatsapp' => env('RCMAA_WHATSAPP', 'https://wa.me/8801400366369'),

        'address' => 'Department of Mathematics, Rajshahi College, Rajshahi 6100, Bangladesh',
        'map' => 'https://maps.google.com/?q=Rajshahi+College,+Rajshahi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact channels, exactly as the association listed them
    |--------------------------------------------------------------------------
    |
    | Three channels, two phone numbers between them, and a helpdesk they did
    | not give a number for — so it is shown as email-only rather than invented.
    |
    */

    'contact_channels' => [
        [
            'label' => 'Official Contact',
            'phone' => '01400-366369',
            'whatsapp' => '01400-366369',
            'email' => 'rcmaa.alumni@gmail.com',
            'hours' => '9:00 AM — 9:00 PM',
        ],
        [
            'label' => 'Registration Helpline',
            'phone' => '01990168773',
            'whatsapp' => '01400-366369',
            'email' => 'rcmaa.alumni@gmail.com',
            'hours' => '9:00 AM — 9:00 PM',
        ],
        [
            'label' => 'Helpdesk',
            'phone' => null,
            'whatsapp' => null,
            'email' => 'rcmaa.alumni@gmail.com',
            'hours' => '9:00 AM — 2:00 PM',
        ],
    ],

    'social' => [
        'facebook' => env('RCMAA_FACEBOOK', 'https://www.facebook.com/share/1LBwi1LCRU/'),
        'linkedin' => env('RCMAA_LINKEDIN', ''),
        'twitter' => env('RCMAA_TWITTER', ''),
        'whatsapp' => env('RCMAA_WHATSAPP', 'https://wa.me/8801400366369'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Department of Mathematics
    |--------------------------------------------------------------------------
    |
    | From the college's own site, https://rc.gov.bd/mathematics/. Note these are
    | the DEPARTMENT's dates — distinct from `college_founded` (1873).
    |
    */

    'department' => [
        'name' => 'Department of Mathematics, Rajshahi College',
        'head' => 'Professor Md. Saiful Islam',
        'email' => 'rc1873math@gmail.com',
        'phone' => '+88 02588855248',
        'students' => 'Approximately 1,500',
        'teachers' => 13,

        'milestones' => [
            1878 => 'I.Sc. and B.Sc. degree courses began',
            1881 => 'Honours course began',
            1893 => 'Masters course began',
            1993 => 'Masters under the National University began',
        ],

        // Heads of department, oldest first, as published by the college.
        'former_heads' => [
            ['বাবু রাজ মোহন সেন, এম, এ', '১৮৮৩–১৯১১'],
            ['বাবু রাজ মোহন সেন, এম, এ', '১৯১২–১৯১৭'],
            ['বাবু রাই চরণ বিশ্বাস, এম, এ', '১৯১৮–১৯১৮'],
            ['বাবু অমৃত লাল চট্টোপাধ্যায়', '১৯৩৪–১৯৪২'],
            ['করুণাময় খাস্তগীর', '১৯৩৭–—'],
            ['মৌলবী আব্দুল করিম মন্ডল', '১৯৪৭–১৯৫৪'],
            ['মৌলবী কে এ এফ এম আবুল কাশেম', '১৯৫৫–১৯৬৩'],
            ['ড. এস. এম. শরফুদ্দীন', '১৯৬৯–১৯৭১'],
            ['দেওয়ান নূরুল হক', '১৯৭০–১৯৯৫'],
            ['মোঃ ইমাম মেহেদী বেগ', '১৯৯৫–১৯৯৭'],
            ['মোঃ আব্দুল লতিফ', '১৯৯৭–১৭-০৪-২০০১'],
            ['রওশন জাহান', '১৮-০৪-২০০১–২২-০৯-২০০৪'],
            ['মোঃ হুমায়ুন কবির', '২৩-০৯-২০০৪–০৫-১১-২০০৪'],
            ['মোহাঃ শফিক উদ্দিন', '০৬-১১-২০০৪–২৯-০৭-২০০৫'],
            ['মোঃ আনোয়ারুল ইসলাম', '৩০-০৭-২০০৫–০১-০৮-২০০৫'],
            ['ড. বায়জুন নাহার', '০২-০৮-২০০৫–২০-০২-২০০৬'],
            ['পরেশ চন্দ্র দাস', '০৭-০১-২০০৬–১৪-০৩-২০০৭'],
            ['মোহাম্মদ মিজানুর রহমান', '১৫-০৩-২০০৭–০৪-০৫-২০০৭'],
            ['মোঃ আমজাদ হোসেন', '০৫-০৫-২০০৭–০৩-০১-২০০৯'],
            ['মোঃ মোশাররফ হোসেন', '০৪-০১-২০০৯–০২-০২-২০০৯'],
            ['ড. মোঃ সিরাজুল ইসলাম', '০৩-০২-২০০৯–০৭-০৬-২০০৯'],
            ['মোঃ মোশাররফ হোসেন', '০৮-০৬-২০০৯–১৬-০৭-২০০৯'],
            ['মোহাঃ শফিক উদ্দিন', '১৮-০৭-২০০৯–২৯-০৪-২০১০'],
            ['মোঃ শহিদুল আলম', '৩০-০৪-২০১০–০১-০৯-২০১০'],
            ['মোঃ মোশাররফ হোসেন', '২০-০৯-২০১০–০৮-০৭-২০১৫'],
            ['মোঃ শহিদুল আলম', '০৯-০৭-২০১৫–০২-১০-২০১৬'],
            ['মহাঃ সিরাজুল ইসলাম', '০৩-১০-২০১৬–২৯-০৯-২০২০'],
            ['মোঃ শহিদুল আলম', '৩০-০৯-২০২০–০১-০১-২০২৩'],
            ['মোঃ কফিলার রহমান', '০২-০১-২০২৩–৩০-০৯-২০২৪'],
            ['ড. আয়েশা নাজনীন', '০১-১০-২০২৪–১৮-১১-২০২৪'],
            ['মোঃ ছাইফুল ইসলাম', '১৯-১১-২০২৪–—'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reunion registration
    |--------------------------------------------------------------------------
    |
    | Registration is priced by category, not a single flat fee — see the
    | association's "Registration Page" specification. Only categories 1 and 2
    | may bring accompanying guests, at BDT 500 each.
    |
    | Fees are in BDT and overridable from .env so the committee can adjust
    | pricing without a deploy.
    |
    */

    'registration' => [
        'open' => env('RCMAA_REGISTRATION_OPEN', true),
        'deadline' => env('RCMAA_REGISTRATION_DEADLINE', '2026-11-30'),
        'event_name' => 'Grand Reunion 2026 — Math Nexus',
        'event_date' => '2026-12-19',
        'photo_max_kb' => 1024,

        // Phone screenshots of a bKash/Nagad confirmation run large, and a bank
        // slip may be a PDF, so this is deliberately looser than the portrait.
        'receipt_max_kb' => 4096,

        'categories' => [
            'teacher' => [
                'number' => '১',
                'icon' => 'graduation',
                'label' => 'Teacher / Former Teacher',
                'label_bn' => 'শিক্ষক/সাবেক শিক্ষক',
                'blurb_bn' => 'রাজশাহী কলেজ গণিত বিভাগের শিক্ষক ও কর্মকর্তা',
                'blurb' => 'Teachers and officers of the Department of Mathematics, Rajshahi College.',
                'fee' => (int) env('RCMAA_FEE_TEACHER', 2535),
                'allows_guests' => true,
                'eligibility_bn' => [],
                'eligibility' => null,
            ],
            'alumni' => [
                'number' => '২',
                'icon' => 'users',
                'label' => 'Alumnus',
                'label_bn' => 'প্রাক্তন শিক্ষার্থী',
                'blurb_bn' => 'রাজশাহী কলেজ গণিত বিভাগ থেকে পাস করেছি',
                'blurb' => 'Graduated from the Department of Mathematics, Rajshahi College.',
                'fee' => (int) env('RCMAA_FEE_ALUMNI', 2535),
                'allows_guests' => true,
                'eligibility_bn' => ['সেশনঃ ২০১৪-১৫ থেকে এর পূর্ববর্তী সকল ব্যাচ সমূহ'],
                'eligibility' => 'Session 2014-15 and every earlier batch.',
            ],
            'recent_graduate' => [
                'number' => '৩',
                'icon' => 'book',
                'label' => 'Recent Graduate',
                'label_bn' => 'সম্প্রতি পাস করেছি',
                'blurb_bn' => 'রাজশাহী কলেজ গণিত বিভাগ থেকে সম্প্রতি পড়া শেষ করেছি',
                'blurb' => 'Recently finished studying at the Department of Mathematics.',
                'fee' => (int) env('RCMAA_FEE_RECENT', 1525),
                'allows_guests' => false,
                'eligibility_bn' => ['অনার্স ও মাস্টার্স কোর্স', 'অনার্স সেশন: ২০১৫-১৬ থেকে ২০১৭-১৮ সেশন পর্যন্ত', 'মাস্টার্স সেশন: ২০১৯-২০ থেকে ২০২১-২২ সেশন পর্যন্ত'],
                'eligibility' => 'Honours sessions 2015-16 to 2017-18; Masters sessions 2019-20 to 2021-22.',
                'qualifies_bn' => "কে 'সম্প্রতি পাস করেছি' হিসেবে গণ্য হবে?",
                'qualifies_note_bn' => 'আপনি যদি নিচের সেশনগুলোর কোনো একটিতে আপনার পড়াশোনা সম্পন্ন করে থাকেন, তাহলে এই বিকল্পটি বেছে নিন।',
            ],
            'current_student' => [
                'number' => '৪',
                'icon' => 'compass',
                'label' => 'Current Student',
                'label_bn' => 'বর্তমান শিক্ষার্থী',
                'blurb_bn' => 'রাজশাহী কলেজ গণিত বিভাগ থেকে পড়ছি',
                'blurb' => 'Currently studying at the Department of Mathematics.',
                'fee' => (int) env('RCMAA_FEE_STUDENT', 1015),
                'allows_guests' => false,
                'eligibility_bn' => ['অনার্স ও মাস্টার্স কোর্স', 'অনার্স সেশন: ২০১৮-১৯ থেকে ২০২৫-২৬ সেশন পর্যন্ত', 'মাস্টার্স সেশন: ২০২২-২৩ থেকে ২০২৩-২৪ সেশন পর্যন্ত'],
                'eligibility' => 'Honours sessions 2018-19 to 2025-26; Masters sessions 2022-23 to 2023-24.',
                'qualifies_bn' => 'কে বর্তমান শিক্ষার্থী হিসেবে নিবন্ধন করতে পারবে?',
                'qualifies_note_bn' => 'আপনি বর্তমানে রাজশাহী কলেজ গণিত বিভাগে নিয়মিতভাবে অধ্যয়নরত।',
            ],
        ],

        // Charged per accompanying guest, for the categories that allow them.
        'guest_fee' => (int) env('RCMAA_FEE_GUEST', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment collection (manual verification)
    |--------------------------------------------------------------------------
    |
    | Registrants send money to these numbers and submit the transaction ID.
    | An admin marks the registration verified from the dashboard. No gateway
    | credentials are involved, so nothing here is secret.
    |
    | The defaults below are deliberately unmistakable placeholders rather than
    | plausible numbers: an account that merely LOOKS right would quietly send
    | registrants' money to the wrong person. Set the real ones in .env before
    | opening registration — the payment step flags any that are still unset.
    |
    */

    'payment' => [
        'methods' => [
            // The association collects through one bKash Merchant account. It is
            // a Merchant, not Personal, account — customers must use "Payment",
            // not "Send Money", or the transfer will not reach it correctly.
            /* 'bkash' => [
                'label' => 'bKash',
                'number' => env('RCMAA_BKASH_NUMBER', '01400366369'),
                'type' => 'Merchant',
                'instruction' => 'Payment',
                'colour' => '#e2136e',
            ], */

            /*
             * Bangla QR — scan-and-pay from any bank or MFS app, at the
             * association's request. There is no number to type; the QR image
             * IS the account. Until the association supplies their QR (drop it
             * at the path below on the public disk), the option hides itself
             * from the form rather than presenting a code that pays nobody.
             */
            'bangla_qr' => [
                'label' => 'Bangla QR',
                'type' => 'Scan & Pay',
                'instruction' => 'Scan with any banking or MFS app',
                'colour' => '#d61f26',
                'qr_image' => 'payment/bangla-qr.png',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Donations
    |--------------------------------------------------------------------------
    |
    | The association's instruction: register and pay the registration fee
    | first, then transfer any donation directly to the bank account. The
    | wording below is theirs, verbatim, in both languages.
    |
    | The account fields are null until the association supplies them — the
    | donation dialog says details are being announced rather than inventing
    | an account for other people's money.
    |
    */

    'donation' => [
        'instruction_bn' => 'যদি আপনি ডোনেশন করতে চান তাহলে প্রথমে রেজিষ্ট্রেশন করে তার পেমেন্ট করুন, এরপর বাকি টাকাটা সরাসরি ব্যাংক অ্যাকাউন্ট-এ ট্রান্সফার করে দিবেন। আপনি ডোনেশন করতে চাইলে ডোনেশন বাটনে ক্লিক করুন।',
        'instruction' => 'If you wish to make a donation, please complete your registration and registration payment first, then transfer your donation amount directly to our bank account. Click the Donation button to proceed.',

        'note_bn' => 'আপনার ডোনেশন সরাসরি ব্যাংকে টাকা পাঠিয়ে দিন।',
        'note' => 'Please send your donation directly to the bank account.',

        'bank' => [
            'account_name' => env('RCMAA_BANK_ACCOUNT_NAME', 'RAJSHAHI COLLEGE MATHEMATICS ALUMNI ASSOCIATION (RCMAA)'),
            'account_number' => env('RCMAA_BANK_ACCOUNT_NUMBER', '6606101046454'),
            'bank' => env('RCMAA_BANK_NAME', 'Pubali Bank PLC'),
            'branch' => env('RCMAA_BANK_BRANCH', 'Court Bazar Islamic Banking Branch'),
            'routing' => env('RCMAA_BANK_ROUTING', '175810059'),
            'swift_code' => env('RCMAA_BANK_SWIFT', 'PUBABDDH'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Form option sets
    |--------------------------------------------------------------------------
    |
    | Shared by the public form, the validation rules and the admin filters so
    | the three can never drift apart.
    |
    */

    'options' => [
        'blood_groups' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],

        'degrees' => [
            'bsc' => 'B.Sc (Honors)',
            'msc' => 'M.Sc',
            'both' => 'B.Sc & M.Sc Both',
            'previous_masters' => 'Previous Masters',
        ],

        'employment_statuses' => [
            'employed' => 'Employed',
            'self_employed' => 'Self-Employed / Business',
            'student_other' => 'Student / Other',
        ],

        'employment_statuses_bn' => [
            'employed' => 'কর্মরত',
            'self_employed' => 'ব্যবসা / স্বনিযুক্ত',
            'student_other' => 'শিক্ষার্থী / অন্যান্য',
        ],

        'tshirt_sizes' => ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'],

        'guest_counts' => ['0' => 'None', '1' => '1', '2' => '2', '3+' => '3+'],

        'profession_types' => [
            'teaching' => 'Teaching · শিক্ষকতা',
            'banking' => 'Banking · ব্যাংকিং',
            'govt_job' => 'Government Job · সরকারি চাকরি',
            'business' => 'Business · ব্যবসা',
            'private_company' => 'Private Company · প্রাইভেট কোম্পানী',
            'multinational_company' => 'Multinational Company · মাল্টিন্যাশনাল কোম্পানী',
            'student' => 'Student · স্টুডেন্ট',
        ],

        'teacher_types' => [
            'teacher' => 'Teacher · শিক্ষক',
            'staff' => 'Employee / Officer · কর্মকর্তা / কর্মচারী',
        ],

        /*
         * Academic sessions, newest first.
         *
         * A dropdown rather than a free text box: the directory groups people by
         * batch, and typed entries arrived as 2008-09, 2008-2009 and ২০০৮-০৯ —
         * one cohort split three ways. The newest entry matches the furthest the
         * association's own category rules reach (current students, honours
         * 2018-19 to 2025-26); the oldest covers anyone plausibly attending.
         */
        'session_newest' => 2025,
        'session_oldest' => 1950,

        'sessions' => (static function (): array {
            $sessions = [];

            for ($year = 2025; $year >= 1950; $year--) {
                $label = sprintf('%d-%02d', $year, ($year + 1) % 100);
                $sessions[$label] = $label;
            }

            return $sessions;
        })(),
    ],

];
