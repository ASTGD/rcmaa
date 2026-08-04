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
            Notice::updateOrCreate(
                ['slug' => Str::slug($notice['title'])],
                [...$notice, 'is_published' => true]
            );
        }
    }

    private function faqs(): void
    {
        $faqs = [
            ['membership', 'Who is eligible to join RCMAA?', 'Any graduate of the Department of Mathematics, Rajshahi College — B.Sc (Honors), M.Sc, both, or Previous Masters — is eligible to join the association. You will be asked for your session and passing year during registration.'],
            ['membership', 'I have lost my class roll and registration number. Can I still register?', 'Yes. The class roll and registration number fields are optional precisely because many alumni no longer have their records. Your name, session and passing year are enough for the committee to verify you.'],
            ['registration', 'How do I register for the Grand Reunion 2026?', 'Complete the registration form on this website. It takes about five minutes and covers your personal, academic and professional details, your T-shirt size, any accompanying guests, and finally your payment information. Your progress is saved in your browser as you go, so you can leave and come back.'],
            ['registration', 'Can I bring my spouse or family?', 'Yes. Select the number of accompanying guests on the reunion step and provide each guest\'s name, relation and occupation. A separate guest fee applies per person and is added to your total automatically.'],
            ['registration', 'I want to perform in the cultural programme. What do I need to do?', 'Select "Yes" on the cultural programme question during registration. Prior registration is mandatory in order to perform on the event day — performers cannot be added at the venue.'],
            ['registration', 'What size should my photograph be?', 'Upload a passport-size photograph in JPG, PNG or WebP format, no larger than 1 MB and at least 200×200 pixels. It is used on your reunion identity card.'],
            ['payment', 'How do I pay the registration fee?', 'Send the total amount to one of the mobile financial service numbers shown on the payment step — bKash, Nagad or Rocket — or transfer it to the association\'s bank account. Then enter the transaction ID, the number you sent it from, and the exact amount paid.'],
            ['payment', 'How long does payment verification take?', 'The committee verifies payments manually, normally within one to two working days. You will receive a confirmation email once your registration is verified, and you can check the status at any time using your reference number.'],
            ['payment', 'I entered the wrong transaction ID. What now?', 'Contact the helpdesk with your reference number and the correct transaction ID. Do not submit a second registration — each transaction ID can only be used once, and duplicate entries slow verification down for everyone.'],
            ['payment', 'Are refunds available?', 'Requests for refunds should be made to the reunion committee through the helpdesk before the registration deadline. Refunds after the deadline are at the committee\'s discretion, since catering and merchandise are ordered against confirmed numbers.'],
            ['events', 'What is included in the registration fee?', 'The registration fee covers your reunion kit, event T-shirt, lunch and refreshments, and entry to the cultural evening. Accompanying guests are covered by the separate guest fee.'],
            ['events', 'When and where is Math Nexus 2026?', 'The Grand Reunion is scheduled for 19 December 2026 on the Rajshahi College campus. The date was approved in writing by the Principal of Rajshahi College on 25 January 2026.'],
            ['general', 'When was RCMAA founded?', 'The association\'s journey began on 16 December 2025 and it took formal shape at a meeting on 3 January 2026, with students from the 2011-12 through 2024-25 sessions taking part. Rajshahi College itself has taught since 1873.'],
            ['general', 'How is my personal information used?', 'Your contact details are used by the committee to administer your registration and are never published. The public alumni directory shows only your name, session, passing year and profession. See the Privacy Policy for the full detail.'],
        ];

        foreach ($faqs as $i => [$category, $question, $answer]) {
            Faq::updateOrCreate(
                ['question' => $question],
                ['category' => $category, 'answer' => $answer, 'sort_order' => $i, 'is_published' => true]
            );
        }
    }
}
