<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Support\RegistrationPricing;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The association wrote its own FAQ and sent it on 4 August 2026. It replaces
 * the English set written before it, which said different things about payment
 * and refunds.
 */
class FaqContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ContentSeeder::class);
    }

    #[Test]
    public function all_twenty_questions_are_published(): void
    {
        $this->assertSame(20, Faq::count());
        $this->assertSame(20, Faq::where('is_published', true)->count());
    }

    #[Test]
    public function the_page_shows_the_associations_own_wording(): void
    {
        $page = $this->get(route('faqs'))->assertOk();

        foreach ([
            'RCMAA কী?',
            'RCMAA-এর সদস্য কারা হতে পারবেন?',
            'Guest Fee কত?',
            'Registration Fee কি ফেরতযোগ্য (Refundable)?',
            'রিইউনিয়নে প্রবেশের জন্য ID Card প্রয়োজন হবে কি?',
            'RCMAA-এর মূল লক্ষ্য কী?',
        ] as $question) {
            $page->assertSee($question, false);
        }
    }

    /** The superseded English answers must not linger beside the new ones. */
    #[Test]
    public function the_replaced_english_faqs_are_gone(): void
    {
        foreach ([
            'How do I pay the registration fee?',
            'Are refunds available?',
            'Who is eligible to join RCMAA?',
            'When and where is Math Nexus 2026?',
        ] as $old) {
            $this->assertNull(Faq::where('question', $old)->first(), "Stale FAQ still present: {$old}");
        }

        $this->get(route('faqs'))->assertOk()->assertDontSee('Are refunds available?');
    }

    /** Re-seeding must not wipe anything the committee adds through /admin. */
    #[Test]
    public function reseeding_keeps_faqs_added_by_the_committee(): void
    {
        Faq::create([
            'question' => 'কমিটির নিজস্ব প্রশ্ন',
            'answer' => 'অ্যাডমিন প্যানেল থেকে যোগ করা।',
            'category' => 'general',
            'sort_order' => 99,
            'is_published' => true,
        ]);

        $this->seed(ContentSeeder::class);

        $this->assertNotNull(Faq::where('question', 'কমিটির নিজস্ব প্রশ্ন')->first());
        $this->assertSame(21, Faq::count());
    }

    /** What the FAQ promises and what the site charges must agree. */
    #[Test]
    public function the_faq_agrees_with_the_configured_fees_and_method(): void
    {
        $this->assertSame(500, RegistrationPricing::guestFee());
        $this->assertStringContainsString(
            '৫০০ টাকা', Faq::where('question', 'Guest Fee কত?')->firstOrFail()->answer
        );

        $this->assertSame(['bkash'], array_keys(config('rcmaa.payment.methods')));
        $this->assertStringContainsString(
            'বিকাশ (bKash)', Faq::where('question', 'Registration Fee কীভাবে পরিশোধ করতে হবে?')->firstOrFail()->answer
        );
    }

    /**
     * The association's two documents disagree about the helpdesk: the FAQ says
     * 10:00–1:00, the contact listing they sent afterwards says 9:00–2:00. The
     * later one drives the site; this test pins both so the discrepancy is
     * visible rather than forgotten, and fails the moment either is corrected.
     */
    #[Test]
    public function the_helpdesk_hours_follow_the_contact_listing(): void
    {
        $this->assertSame('09:00 AM — 02:00 PM', config('rcmaa.contact.helpdesk_hours'));

        $this->assertStringContainsString(
            'সকাল ১০:০০টা থেকে দুপুর ১:০০টা',
            Faq::where('question', 'অনলাইনে Registration করতে সমস্যা হলে কী করবো?')->firstOrFail()->answer,
            'The FAQ still says 10-1 — the association needs to say which is right.'
        );
    }
}
