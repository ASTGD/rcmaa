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
    | Channels and hours are taken from the association's contact page.
    | +880 1643-740416 is the single public number across the whole site.
    |
    */

    'contact' => [
        'email' => env('RCMAA_EMAIL', 'rcmaa.alumni@gmail.com'),
        'phone' => env('RCMAA_PHONE', '+880 1643-740416'),
        'hotline' => env('RCMAA_HOTLINE', '+880 1643-740416'),
        'hotline_hours' => '09:00 AM — 09:00 PM',
        'helpdesk' => env('RCMAA_HELPDESK', '+880 1643-740416'),
        // The association's FAQ (4 Aug 2026) states 10:00 AM to 1:00 PM.
        'helpdesk_hours' => '10:00 AM — 01:00 PM',
        // Donations above the registration fee are arranged by phone first.
        'donation' => env('RCMAA_DONATION_PHONE', '০১৪০০-৩৬৬৩৬৯'),
        'whatsapp' => env('RCMAA_WHATSAPP', 'https://wa.link/u9bvuh'),
        'address' => 'Department of Mathematics, Rajshahi College, Rajshahi 6100, Bangladesh',
        'map' => 'https://maps.google.com/?q=Rajshahi+College,+Rajshahi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact channels, as published on the association's contact page
    |--------------------------------------------------------------------------
    */

    'contact_channels' => [
        ['label' => 'Official Contact', 'phone' => '+880 1643-740416', 'email' => 'rcmaa.alumni@gmail.com', 'hours' => '9:00 AM — 9:00 PM'],
        ['label' => 'Registration Helpline', 'phone' => '+880 1643-740416', 'email' => 'rcmaa.alumni@gmail.com', 'hours' => '9:00 AM — 9:00 PM'],
        ['label' => 'Helpdesk', 'phone' => '+880 1643-740416', 'email' => null, 'hours' => '10:00 AM — 1:00 PM'],
    ],

    'social' => [
        'facebook' => env('RCMAA_FACEBOOK', 'https://www.facebook.com/share/1LBwi1LCRU/'),
        'linkedin' => env('RCMAA_LINKEDIN', ''),
        'twitter' => env('RCMAA_TWITTER', ''),
        'whatsapp' => env('RCMAA_WHATSAPP', 'https://wa.link/u9bvuh'),
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
            ['Babu Raj Mohan Sen, M.A.', '1912–1917'],
            ['Babu Rai Charan Biswas, M.A.', '1917–1918'],
            ['Omdatul Islam', '1934–1935'],
            ['Babu Amrita Lal Chatterji', '1935–1942'],
            ['Maulvi Abdul Karim Mondal', '1947–1954'],
            ['Maulvi K.A.F.M. Abul Quasem', '1955–1963'],
            ['D. S. M. Sharfuddin', '1969–1971'],
            ['Md. Emam Mehadi Beg', '1995–1997'],
            ['Md. Shafiq Uddin', '2004–2005, 2009–2010'],
            ['Dr. Baitun Nahar', '2005–2006'],
            ['Parash Chandra Das', '2006–2007'],
            ['Dr. Md. Sirazul Islam', '2009'],
            ['Golam Kabir', '2014'],
            ['Md. Serajul Islam', '2016–2020'],
            ['Md. Shahidul Alam', '2016–present'],
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
            'bkash' => [
                'label' => 'bKash',
                'number' => env('RCMAA_BKASH_NUMBER', '01400366369'),
                'type' => 'Merchant',
                'instruction' => 'Payment',
                'colour' => '#e2136e',
            ],
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
