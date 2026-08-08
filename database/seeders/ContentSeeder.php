<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Notice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Content carried over from the previous rcmaa.bd site.
 *
 * Names, roles, sessions, event dates, photographs and gallery captions are the
 * association's own, taken from its home page, its Bangla "Our goal" page and
 * its media library.
 *
 * Note on scope: the old site's Committee, Faculty, Notice, Event and Gallery
 * *pages* were never written — they still carried the WordPress theme's
 * "Universite" demo text and stock portraits ("Prof. Dr. Alex Thunder" and so
 * on). Everything seeded below is therefore drawn only from the parts of the
 * site that carried genuine content. The five remaining committees and the
 * faculty list have no source data yet and are left empty rather than invented.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->events();
        $this->gallery();
        $this->notices();
        $this->faqs();
    }

    private function events(): void
    {
        $events = [
            [
                'title' => 'General Meeting',
                'starts_on' => '2026-08-01',
                'excerpt' => 'A general meeting of the Rajshahi College Mathematics Alumni Association to review reunion preparations and association business.',
                'venue' => 'Department of Mathematics, Rajshahi College',
                'cover_path' => 'events/general-meeting.png',
                'registration_open' => false,
            ],
            [
                'title' => 'Math Nexus — Grand Reunion 2026',
                'starts_on' => '2026-12-19',
                'excerpt' => 'The grand reunion of the Department of Mathematics: reconnect with old friends, network with fellow graduates and celebrate our shared legacy.',
                'body' => "Math Nexus is the association's largest undertaking to date.\n\nIt is not merely a reunion, but an attempt to build a strong bridge of relationships, memories, experience and cooperation from one generation of the Department of Mathematics to the next.\n\nWritten approval for the event was received from the Principal of Rajshahi College on 25 January 2026.",
                'venue' => 'Rajshahi College Campus, Rajshahi',
                'start_time' => '9:00 AM — 9:00 PM',
                'cover_path' => 'events/math-nexus.png',
                'registration_open' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['slug' => Str::slug($event['title'])],
                [...$event, 'is_published' => true]
            );
        }
    }

    private function gallery(): void
    {
        // Captions are verbatim from the old home page. Each file was matched to
        // its caption by inspecting the photograph itself, since the gallery
        // widget did not expose the pairing in its markup.
        $items = [
            ['convenor', 'Convenor', 'Convener at a meeting about websites in the classroom.', 'events'],
            ['classroom', 'Mathematics Department Classroom', 'Teachers of the Mathematics Department of Rajshahi College in a meeting in the classroom.', 'department'],
            ['campus', 'Inside the Campus', 'Presence of students on campus during recreation.', 'campus'],
            ['attendance-award', 'Attendance Award', 'Highest attendance award of the Rajshahi College Mathematics Department.', 'awards'],
            ['logo-competition', 'Logo Competition', 'Logo competition organised by the Rajshahi College Mathematics Department.', 'clubs'],
            ['departmental-club', 'Departmental Club', 'Students present at the Rajshahi College Mathematics Department Club.', 'clubs'],

            // Supplied by the association via Google Drive (Gallery Page / 3. Images).
            // Titles are descriptive of what the photographs show — the committee
            // should retitle them if it has its own captions.
            ['administration-building', 'At the Administration Building', 'Members of the association on the steps of the Rajshahi College administration building, 25 January 2026 — the day the Principal\'s written approval for the reunion was received.', 'events', 'jpg'],
            ['department-meeting', 'Departmental Meeting', 'Teachers and students of the Department of Mathematics in a meeting in the classroom.', 'events', 'jpg'],
            ['general-meeting', 'General Meeting', 'Students of the department gathered for a general meeting of the association.', 'events', 'jpg'],
            ['department-office', 'Department Office', 'The departmental office, where registration and record-keeping work is carried out.', 'department', 'jpg'],
        ];

        foreach ($items as $i => $item) {
            [$slug, $title, $caption, $category] = $item;
            $ext = $item[4] ?? 'jpg';
            GalleryItem::updateOrCreate(
                ['title' => $title],
                [
                    'caption' => $caption,
                    'category' => $category,
                    'image_path' => "gallery/{$slug}.{$ext}",
                    'sort_order' => $i,
                    'is_featured' => true,
                    'is_published' => true,
                ]
            );
        }
    }

    private function notices(): void
    {
        $notices = [
            [
                // The association's own opening line for the home-page ticker,
                // verbatim from their 8 Aug 2026 requirements document.
                'title' => 'খুব শীঘ্রই রেজিষ্ট্রেশন কার্যক্রম শুরু হতে যাচ্ছে। আমাদের গ্রান্ড রিইউনিয়ন: Math Nexus - RCMAA Reunion 2026 আগামী ১৯ শে ডিসেম্বর ২০২৬।',
                'slug' => 'reunion-2026-announcement',
                'published_on' => '2026-08-08',
                'excerpt' => 'Registration process will start very soon. Our Grand Reunion: Math Nexus - RCMAA Reunion 2026 will be held on December 19, 2026.',
                'body' => "খুব শীঘ্রই রেজিষ্ট্রেশন কার্যক্রম শুরু হতে যাচ্ছে। আমাদের গ্রান্ড রিইউনিয়ন: **Math Nexus - RCMAA Reunion 2026** আগামী ১৯ শে ডিসেম্বর ২০২৬।\n\nRegistration process will start very soon. Our Grand Reunion: **Math Nexus - RCMAA Reunion 2026** will be held on December 19, 2026.",
                'is_pinned' => true,
            ],
            [
                'title' => 'Registration open for the Grand Reunion 2026',
                'published_on' => '2026-07-01',
                'excerpt' => 'Online registration for Math Nexus — the Grand Reunion of the Department of Mathematics — is now open.',
                'body' => "Online registration for **Math Nexus, the Grand Reunion 2026**, is now open to all graduates of the Department of Mathematics, Rajshahi College.\n\nComplete the registration form on this website, pay the registration fee to one of the listed mobile financial service numbers, and enter your transaction ID on the final step. Your registration will be verified by the committee within one to two working days, after which you can confirm it using your reference number.\n\nPlease note that prior registration is mandatory in order to perform in the cultural programme on the event day.",
                'is_pinned' => true,
            ],
            [
                'title' => 'General Meeting — 01 August',
                'published_on' => '2026-07-20',
                'excerpt' => 'All members are requested to attend the general meeting of the association on 01 August.',
                'body' => "All members of the Rajshahi College Mathematics Alumni Association are requested to attend the general meeting on **01 August** at the Department of Mathematics, Rajshahi College.\n\nThe agenda covers reunion preparations, the sub-committee reports and the association's ongoing business.",
            ],
        ];

        foreach ($notices as $notice) {
            // A Bangla title slugs to an empty string, so those carry their own.
            Notice::updateOrCreate(
                ['slug' => $notice['slug'] ?? Str::slug($notice['title'])],
                [...$notice, 'is_published' => true]
            );
        }
    }

    /**
     * The association's own FAQ, supplied 4 August 2026, in their wording.
     *
     * Kept in Bangla because that is how they wrote it and how their alumni
     * read it; the English terms they mixed in are theirs too. The earlier
     * English set this replaces is removed by question below, rather than
     * clearing the table, so anything an admin adds through /admin survives a
     * re-seed.
     */
    private function faqs(): void
    {
        $faqs = [
            ['general', 'RCMAA কী?',
                'Rajshahi College Mathematics Alumni Association (RCMAA) হলো রাজশাহী কলেজের গণিত বিভাগের বর্তমান ও প্রাক্তন শিক্ষার্থীদের একটি স্থায়ী প্ল্যাটফর্ম। এর মূল উদ্দেশ্য হলো Alumni Network গড়ে তোলা, পারস্পরিক যোগাযোগ বৃদ্ধি করা এবং বিভিন্ন শিক্ষামূলক, সামাজিক ও পুনর্মিলনী কার্যক্রমের মাধ্যমে সকলকে একটি বন্ধনে যুক্ত রাখা।'],

            ['membership', 'RCMAA-এর সদস্য কারা হতে পারবেন?',
                'রাজশাহী কলেজের গণিত বিভাগের সকল বর্তমান ও প্রাক্তন শিক্ষার্থী RCMAA-এর সদস্য হতে পারবেন।'],

            ['membership', 'সদস্য হওয়ার নিয়ম কী?',
                'প্রথম Math Nexus – RCMAA Reunion 2026-এ যারা সফলভাবে নিবন্ধন (Registration) সম্পন্ন করবেন, তারাই RCMAA-এর সদস্য হিসেবে অন্তর্ভুক্ত হবেন।'],

            ['events', 'Math Nexus – RCMAA Reunion 2026 কবে অনুষ্ঠিত হবে?',
                'রিইউনিয়ন আগামী ১৯ ডিসেম্বর ২০২৬ তারিখে অনুষ্ঠিত হবে।'],

            ['events', 'রিইউনিয়ন কোথায় অনুষ্ঠিত হবে?',
                'অনুষ্ঠানটি রাজশাহী কলেজ প্রাঙ্গণে অনুষ্ঠিত হবে।'],

            ['events', 'কারা রিইউনিয়নে অংশগ্রহণ করতে পারবেন?',
                'রাজশাহী কলেজের গণিত বিভাগের বর্তমান ও প্রাক্তন শিক্ষার্থী, শিক্ষক এবং কর্মচারীবৃন্দ রিইউনিয়নে অংশগ্রহণ করতে পারবেন।'],

            ['registration', 'অতিথি (Guest) আনা যাবে কি?',
                'হ্যাঁ। শিক্ষক, কর্মচারী এবং প্রাক্তন শিক্ষার্থীরা নির্ধারিত নিয়ম অনুযায়ী অতিথি (Guest) নিয়ে অংশগ্রহণ করতে পারবেন।'],

            ['payment', 'Guest Fee কত?',
                'প্রতিজন অতিথির জন্য নির্ধারিত ফি ৫০০ টাকা।'],

            ['registration', 'কীভাবে Registration সম্পন্ন করতে হবে?',
                'রেজিস্ট্রেশন শুধুমাত্র অনলাইনের মাধ্যমে সম্পন্ন করা যাবে। কোনো অফলাইন রেজিস্ট্রেশন ব্যবস্থা থাকবে না।'],

            ['registration', 'অনলাইনে Registration করতে সমস্যা হলে কী করবো?',
                'রেজিস্ট্রেশন করতে কোনো সমস্যা হলে রাজশাহী কলেজের গণিত বিভাগের Help Desk-এ সকাল ১০:০০টা থেকে দুপুর ১:০০টা-এর মধ্যে যোগাযোগ করলে প্রয়োজনীয় সহযোগিতা প্রদান করা হবে।'],

            ['payment', 'Registration Fee কীভাবে পরিশোধ করতে হবে?',
                'রেজিস্ট্রেশন ফি শুধুমাত্র বিকাশ (bKash)-এর মাধ্যমে পরিশোধ করা যাবে।'],

            ['payment', 'Registration Fee কি ফেরতযোগ্য (Refundable)?',
                'না। একবার রেজিস্ট্রেশন সম্পন্ন হলে কোনো অবস্থাতেই Registration Fee ফেরতযোগ্য হবে না।'],

            ['general', 'ওয়েবসাইটে Login করার সুবিধা থাকবে কি?',
                'হ্যাঁ। সদস্যরা নিজস্ব অ্যাকাউন্টে লগইন করতে পারবেন।'],

            ['general', 'Alumni Database কে দেখতে পারবেন?',
                'শুধুমাত্র RCMAA-এর নিবন্ধিত সদস্যরাই লগইন করার মাধ্যমে Alumni Database দেখতে পারবেন।'],

            ['events', 'রিইউনিয়নে প্রবেশের জন্য ID Card প্রয়োজন হবে কি?',
                'হ্যাঁ। অনুষ্ঠানে প্রবেশের জন্য অফিসিয়াল RCMAA Reunion ID Card প্রদর্শন বাধ্যতামূলক।'],

            ['events', 'অনুষ্ঠানে খাবারের ব্যবস্থা থাকবে কি?',
                'হ্যাঁ। সকল নিবন্ধিত অংশগ্রহণকারীর জন্য সকালের নাস্তা এবং দুপুরের খাবারের ব্যবস্থা থাকবে।'],

            ['events', 'সাংস্কৃতিক অনুষ্ঠান থাকবে কি?',
                'হ্যাঁ। রিইউনিয়নের অংশ হিসেবে একটি Grand Cultural Program আয়োজন করা হবে।'],

            ['events', 'স্মরণিকা (Souvenir) প্রকাশ করা হবে কি?',
                'হ্যাঁ। রিইউনিয়ন উপলক্ষে একটি স্মরণিকা প্রকাশ করা হবে।'],

            ['general', 'কোনো সমস্যা বা তথ্যের প্রয়োজন হলে কোথায় যোগাযোগ করবো?',
                'যেকোনো ধরনের সহযোগিতার জন্য রাজশাহী কলেজের গণিত বিভাগের Help Desk-এ সরাসরি যোগাযোগ করতে পারবেন। ই-মেইল: rcmaa.alumni@gmail.com। Facebook Page: Rajshahi College Mathematics Alumni Association (RCMAA)।'],

            ['general', 'RCMAA-এর মূল লক্ষ্য কী?',
                'RCMAA-এর প্রধান লক্ষ্য হলো গণিত বিভাগের বর্তমান ও প্রাক্তন শিক্ষার্থীদের মধ্যে একটি শক্তিশালী, স্থায়ী ও কার্যকর Alumni Network গড়ে তোলা; পারস্পরিক সহযোগিতা বৃদ্ধি করা এবং ভবিষ্যৎ প্রজন্মের জন্য একটি সুসংগঠিত Alumni Community প্রতিষ্ঠা করা।'],
        ];

        foreach ($faqs as $i => [$category, $question, $answer]) {
            Faq::updateOrCreate(
                ['question' => $question],
                ['category' => $category, 'answer' => $answer, 'sort_order' => $i, 'is_published' => true]
            );
        }

        // The English set written before the association sent their own. Removed
        // by question so nothing added through /admin is caught in the sweep.
        Faq::whereIn('question', [
            'Who is eligible to join RCMAA?',
            'I have lost my class roll and registration number. Can I still register?',
            'How do I register for the Grand Reunion 2026?',
            'Can I bring my spouse or family?',
            'I want to perform in the cultural programme. What do I need to do?',
            'What size should my photograph be?',
            'How do I pay the registration fee?',
            'How long does payment verification take?',
            'I entered the wrong transaction ID. What now?',
            'Are refunds available?',
            'What is included in the registration fee?',
            'When and where is Math Nexus 2026?',
            'When was RCMAA founded?',
            'How is my personal information used?',
        ])->delete();
    }
}
