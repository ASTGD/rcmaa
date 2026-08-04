<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Support\RegistrationPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The association prices registration by category, and only two of the four may
 * bring guests. Getting either wrong means charging people the wrong amount.
 */
class RegistrationCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category' => 'alumni',
            'full_name_en' => 'Md. Rofikul Islam',
            'mobile' => '01712345678',
            'email' => 'rofikul@example.com',
            'present_address' => 'Rajshahi',
            'session' => '2008-09',
            'degree' => 'both',
            'masters_session' => '2012-13',
            'passing_year' => 2012,
            'employment_status' => 'student_other',
            'tshirt_size' => 'XL',
            'cultural_program' => '1',
            'guest_count' => '0',
            'guests' => [],
            'payment_method' => 'bkash',
            'transaction_id' => 'A1B2C3D4E5',
            'sender_number' => '01712345678',
            'amount_paid' => RegistrationPricing::fee('alumni'),
            'terms' => '1',
        ], $overrides);
    }

    public static function categories(): array
    {
        return [
            'teacher' => ['teacher', 2535, true],
            'alumni' => ['alumni', 2535, true],
            'recent graduate' => ['recent_graduate', 1525, false],
            'current student' => ['current_student', 1015, false],
        ];
    }

    #[Test]
    #[DataProvider('categories')]
    public function each_category_charges_its_own_fee(string $key, int $fee, bool $guests): void
    {
        $this->assertSame($fee, RegistrationPricing::fee($key));
        $this->assertSame($guests, RegistrationPricing::allowsGuests($key));
    }

    #[Test]
    #[DataProvider('categories')]
    public function a_registration_records_the_fee_for_its_category(string $key, int $fee, bool $guests): void
    {
        Mail::fake();

        $this->post(route('register.store'), $this->payload([
            'category' => $key,
            'amount_paid' => $fee,
        ]))->assertSessionHasNoErrors();

        $registration = Registration::sole();

        $this->assertSame($key, $registration->category);
        $this->assertSame($fee, $registration->amount_due);
        $this->assertSame($fee, $registration->category_fee);
    }

    #[Test]
    public function guests_cost_five_hundred_each_for_the_categories_that_allow_them(): void
    {
        $this->assertSame(500, RegistrationPricing::guestFee());
        $this->assertSame(2535 + 500, RegistrationPricing::total('alumni', 1));
        $this->assertSame(2535 + 1000, RegistrationPricing::total('teacher', 2));
    }

    #[Test]
    public function categories_that_disallow_guests_are_never_charged_for_them(): void
    {
        $this->assertSame(1525, RegistrationPricing::total('recent_graduate', 3));
        $this->assertSame(1015, RegistrationPricing::total('current_student', 2));
    }

    #[Test]
    public function a_current_student_cannot_register_guests(): void
    {
        $this->post(route('register.store'), $this->payload([
            'category' => 'current_student',
            'guest_count' => '1',
            'guests' => [['name' => 'Someone', 'relation' => 'Friend', 'occupation' => '-']],
            'amount_paid' => 1015 + 500,
        ]))->assertSessionHasErrors('guest_count');

        $this->assertSame(0, Registration::count());
    }

    #[Test]
    public function paying_the_old_flat_fee_is_rejected_for_a_pricier_category(): void
    {
        // 2,000 was the previous flat rate; it no longer covers any full category.
        $this->post(route('register.store'), $this->payload([
            'category' => 'teacher',
            'amount_paid' => 2000,
        ]))->assertSessionHasErrors('amount_paid');
    }

    #[Test]
    public function an_unknown_category_is_rejected(): void
    {
        $this->post(route('register.store'), $this->payload(['category' => 'vip']))
            ->assertSessionHasErrors('category');
    }

    #[Test]
    public function the_form_shows_all_four_categories_with_their_fees(): void
    {
        $this->get(route('register.create'))
            ->assertOk()
            ->assertSee('আপনার শ্রেণী নির্বাচন করুন', false)
            ->assertSee('শিক্ষক/সাবেক শিক্ষক', false)
            ->assertSee('প্রাক্তন শিক্ষার্থী', false)
            ->assertSee('সম্প্রতি পাস করেছি', false)
            ->assertSee('বর্তমান শিক্ষার্থী', false)
            ->assertSee('2,535')
            ->assertSee('1,525')
            ->assertSee('1,015');
    }

    /**
     * The session rules for categories 3 and 4 run to several lines and made
     * step 1 nearly three screens on a phone, so they fold away behind a
     * toggle. The rules must still be present in the markup — collapsed, not
     * dropped — and the toggle sits inside the card's <label>, so its click has
     * to be stopped or reading the rules would silently select that category
     * and change what somebody pays.
     */
    #[Test]
    public function the_long_eligibility_rules_are_collapsed_but_still_present(): void
    {
        $body = $this->get(route('register.create'))->assertOk()->getContent();

        foreach (['recent_graduate', 'current_student'] as $key) {
            $this->assertStringContainsString('id="eligibility-'.$key.'"', $body);
            $this->assertStringContainsString('aria-controls="eligibility-'.$key.'"', $body);
        }

        // The toggle must not fall through to the card's radio.
        $this->assertStringContainsString('@click.stop.prevent="open = ! open"', $body);

        // Every rule the association wrote is still in the page.
        foreach (config('rcmaa.registration.categories') as $cat) {
            foreach ((array) ($cat['eligibility_bn'] ?? []) as $line) {
                $this->assertStringContainsString($line, $body, "Eligibility line dropped: {$line}");
            }
        }
    }

    /** The one-line rule on the Alumnus card stays in plain sight. */
    #[Test]
    public function the_short_eligibility_rule_is_not_hidden(): void
    {
        $this->get(route('register.create'))
            ->assertOk()
            ->assertSee('সেশনঃ ২০১৪-১৫ থেকে এর পূর্ববর্তী সকল ব্যাচ সমূহ', false)
            ->assertDontSee('id="eligibility-alumni"', false);
    }
}
