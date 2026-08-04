<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The register page carries a hidden guest_count fallback alongside the radio
 * group. x-show hides the radios with CSS but does not stop the browser
 * submitting them, so the request contains the name twice and PHP keeps the
 * last one. While the hidden field sat below the group it won every time, and
 * anybody bringing a guest was told "You selected 0 guest(s) but provided
 * details for 1" with no way to get past it.
 *
 * These tests pin the ordering and the outcome.
 */
class GuestCountTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category' => 'alumni',
            'full_name_en' => 'Md. Shafiqul Alam',
            'mobile' => '01712345678',
            'email' => 'shafiqul@example.test',
            'present_address' => 'Boalia, Rajshahi',
            'session' => '2008-09',
            'degree' => 'both',
            'masters_session' => '2012-13',
            'passing_year' => 2012,
            'employment_status' => 'employed',
            'profession' => 'Education',
            'organization' => 'Rajshahi College',
            'tshirt_size' => 'XL',
            'cultural_program' => '1',
            'payment_method' => 'bkash',
            'transaction_id' => 'BR7K2M9XQ4',
            'sender_number' => '01712345678',
            'terms' => '1',
        ], $overrides);
    }

    /**
     * The guard itself: whatever else changes on the page, the fallback must
     * come before the radios or the browser's last-wins overrides real input.
     */
    #[Test]
    public function the_hidden_fallback_appears_before_the_radio_group(): void
    {
        $body = $this->get(route('register.create'))->assertOk()->getContent();

        preg_match_all('/name="guest_count" value="([^"]*)"/', $body, $m, PREG_OFFSET_CAPTURE);
        $controls = $m[1];

        $this->assertGreaterThan(1, count($controls), 'Expected a fallback plus the radio options.');
        $this->assertSame('0', $controls[0][0], 'The first guest_count control must be the 0 fallback.');

        $hiddenPos = strpos($body, '<input type="hidden" name="guest_count" value="0">');
        $firstRadio = strpos($body, 'name="guest_count" value="1"');

        $this->assertNotFalse($hiddenPos);
        $this->assertNotFalse($firstRadio);
        $this->assertLessThan(
            $firstRadio, $hiddenPos,
            'The hidden guest_count fallback must precede the radios, or it overrides them.'
        );
    }

    #[Test]
    public function a_registration_with_one_guest_is_recorded_and_priced(): void
    {
        $this->post(route('register.store'), $this->payload([
            'guest_count' => '1',
            'guests' => [['name' => 'Shirin Akter', 'relation' => 'Spouse', 'occupation' => 'Teacher']],
            'amount_paid' => 3035,
        ]))->assertRedirect()->assertSessionHasNoErrors();

        $r = Registration::firstOrFail();
        $this->assertSame('1', $r->guest_count);
        $this->assertCount(1, $r->guests);
        $this->assertSame('Shirin Akter', $r->guests[0]['name']);
        $this->assertSame(3035, $r->amount_due);
    }

    #[Test]
    public function the_fee_covers_the_guest(): void
    {
        $this->post(route('register.store'), $this->payload([
            'guest_count' => '2',
            'guests' => [
                ['name' => 'Shirin Akter', 'relation' => 'Spouse', 'occupation' => 'Teacher'],
                ['name' => 'Rafi Alam', 'relation' => 'Son', 'occupation' => 'Student'],
            ],
            'amount_paid' => 3535,
        ]))->assertRedirect()->assertSessionHasNoErrors();

        $r = Registration::firstOrFail();
        $this->assertSame(2535 + 2 * 500, $r->amount_due);
        $this->assertSame(0, $r->balance, 'Paying the exact total must not read as a discrepancy.');
    }

    #[Test]
    public function underpaying_for_a_guest_is_refused(): void
    {
        $this->post(route('register.store'), $this->payload([
            'guest_count' => '1',
            'guests' => [['name' => 'Shirin Akter', 'relation' => 'Spouse', 'occupation' => 'Teacher']],
            'amount_paid' => 2535, // forgot the guest
        ]))->assertSessionHasErrors('amount_paid');

        $this->assertSame(0, Registration::count());
    }

    #[Test]
    public function a_category_that_bars_guests_still_registers(): void
    {
        $this->post(route('register.store'), $this->payload([
            'category' => 'current_student',
            'session' => '2022-23',
            'passing_year' => 2026,
            'employment_status' => 'student_other',
            'guest_count' => '0',
            'amount_paid' => 1015,
        ]))->assertRedirect()->assertSessionHasNoErrors();

        $r = Registration::firstOrFail();
        $this->assertSame('0', $r->guest_count);
        $this->assertSame(1015, $r->amount_due);
        $this->assertSame(0, $r->guest_fee, 'Guests are not permitted, so no guest rate applies.');
    }

    #[Test]
    public function a_guest_sneaked_into_a_barred_category_is_refused(): void
    {
        $this->post(route('register.store'), $this->payload([
            'category' => 'current_student',
            'session' => '2022-23',
            'passing_year' => 2026,
            'employment_status' => 'student_other',
            'guest_count' => '1',
            'guests' => [['name' => 'Someone', 'relation' => 'Friend', 'occupation' => 'None']],
            'amount_paid' => 1515,
        ]))->assertSessionHasErrors('guest_count');

        $this->assertSame(0, Registration::count());
    }
}
