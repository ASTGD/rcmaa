<?php

namespace Tests\Feature;

use App\Support\PaymentMethods;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentConfigTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A collection account left on its placeholder must never be shown as
     * payable — registrants would send money into the void.
     */
    #[Test]
    public function it_flags_payment_accounts_that_are_still_placeholders(): void
    {
        config(['rcmaa.payment.methods.bkash.number' => '01XXX-XXXXXX']);

        $this->get(route('register.create'))
            ->assertOk()
            ->assertSee('Not configured — do not send money to this method.', false)
            ->assertSee('Some payment accounts are not set up yet');
    }

    #[Test]
    public function it_shows_configured_accounts_normally(): void
    {
        config(['rcmaa.payment.methods' => [
            'bkash' => ['label' => 'bKash', 'number' => '01712345678', 'type' => 'Personal', 'colour' => '#e2136e'],
        ]]);

        $this->get(route('register.create'))
            ->assertOk()
            ->assertSee('01712345678')
            ->assertDontSee('Not configured');
    }

    /**
     * The association listed three channels on 4 August 2026: an Official
     * Contact line, a separate Registration Helpline, and a Helpdesk they gave
     * no number for. The last one matters — a tel: link built from a null
     * number is a dead link on every page that renders it.
     */
    #[Test]
    public function the_contact_details_are_the_ones_the_association_listed(): void
    {
        $this->assertSame('01400-366369', config('rcmaa.contact.phone'));
        $this->assertSame('01990168773', config('rcmaa.contact.helpline'));
        $this->assertNull(config('rcmaa.contact.helpdesk'), 'No helpdesk number was supplied.');

        $this->get(route('contact'))->assertOk()
            ->assertSee('01400-366369')
            ->assertSee('01990168773')
            ->assertSee('rcmaa.alumni@gmail.com');

        $this->get(route('home'))->assertOk()->assertSee('01400-366369');
    }

    #[Test]
    public function whatsapp_points_at_the_official_number(): void
    {
        $this->assertSame('https://wa.me/8801400366369', config('rcmaa.contact.whatsapp'));

        $this->get(route('contact'))->assertOk()->assertSee('wa.me/8801400366369', false);
    }

    /** A missing number must not render an empty tel: link anywhere. */
    #[Test]
    public function pages_that_show_contact_channels_survive_the_missing_number(): void
    {
        foreach (['contact', 'help-center', 'faqs', 'register.create'] as $route) {
            $body = $this->get(route($route))->assertOk()->getContent();
            $this->assertStringNotContainsString('href="tel:"', $body, "Empty tel: link on {$route}");
            $this->assertStringNotContainsString('tel:"', $body, "Empty tel: link on {$route}");
        }
    }

    /**
     * The association collects through one bKash Merchant account. Two details
     * matter and both are easy to get wrong: the number itself, and the fact
     * that a Merchant account needs "Payment" rather than "Send Money" — money
     * sent the wrong way does not arrive correctly.
     */
    #[Test]
    public function bkash_is_the_only_offered_method_and_it_is_a_merchant_account(): void
    {
        // Bangla QR is declared but gated behind its QR image existing on the
        // public disk. No image in tests, so bKash is the whole offer — and
        // the validator must refuse bangla_qr while that holds.
        $methods = PaymentMethods::available();

        $this->assertSame(['bkash'], array_keys($methods));
        $this->assertSame('01400366369', $methods['bkash']['number']);
        $this->assertSame('Merchant', $methods['bkash']['type']);
    }

    #[Test]
    public function bangla_qr_is_offered_once_its_image_exists(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            config('rcmaa.payment.methods.bangla_qr.qr_image'), 'png-bytes'
        );

        $this->assertSame(['bkash', 'bangla_qr'], PaymentMethods::keys());
    }

    #[Test]
    public function the_form_tells_people_to_use_payment_not_send_money(): void
    {
        $body = $this->get(route('register.create'))->assertOk()->getContent();

        $this->assertStringContainsString('01400366369', $body);
        $this->assertStringContainsString('Merchant', $body);
        $this->assertStringContainsString('&ldquo;Payment&rdquo;', $body);
        $this->assertStringNotContainsString(
            'Use the &ldquo;Send Money&rdquo; option', $body,
            'The old Personal-account instruction must be gone.'
        );

        // The retired methods must not be offered anywhere on the form.
        foreach (['Nagad', 'Rocket', 'Bank Transfer'] as $gone) {
            $this->assertStringNotContainsString($gone, $body, "{$gone} should no longer be offered.");
        }
    }

    #[Test]
    public function a_registration_can_only_be_paid_by_bkash(): void
    {
        $payload = [
            'category' => 'alumni', 'full_name_en' => 'Payment Method Check',
            'mobile' => '01712345678', 'email' => 'pm@example.test',
            'password' => 'reunion2026', 'password_confirmation' => 'reunion2026',
            'present_address' => 'Rajshahi', 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba', 'session' => '2008-09',
            'degree' => 'bsc', 'passing_year' => 2012,
            'employment_status' => 'employed', 'profession' => 'Education',
            'organization' => 'Rajshahi College', 'tshirt_size' => 'L',
            'cultural_program' => '0', 'guest_count' => '0',
            'photo' => UploadedFile::fake()->image('portrait.jpg', 400, 500),
            'transaction_id' => 'BKASHONLY1', 'sender_number' => '01712345678',
            'amount_paid' => 2535, 'terms' => '1',
        ];

        $this->post(route('register.store'), ['payment_method' => 'nagad'] + $payload)
            ->assertSessionHasErrors('payment_method');

        // Declared in config, but not offered until its QR image exists — so
        // the server must refuse it too.
        $this->post(route('register.store'), ['payment_method' => 'bangla_qr'] + $payload)
            ->assertSessionHasErrors('payment_method');

        $this->post(route('register.store'), ['payment_method' => 'bkash'] + $payload)
            ->assertRedirect()->assertSessionHasNoErrors();
    }

    /**
     * The association asked for a donation notice on the payment step. It has
     * to be read before somebody pays, so it sits with the account details
     * rather than lower down the step.
     */
    #[Test]
    public function the_payment_step_carries_the_donation_notice(): void
    {
        $body = $this->get(route('register.create'))->assertOk()->getContent();

        // The association's 8 Aug 2026 wording, verbatim, both languages.
        $this->assertStringContainsString(e(config('rcmaa.donation.instruction_bn')), $body);
        $this->assertStringContainsString(e(config('rcmaa.donation.instruction')), $body);
        $this->assertStringContainsString(e(config('rcmaa.donation.note_bn')), $body);

        // The Donation button, and the association's bank account, which they
        // supplied on 9 Aug 2026 — so the panel now discloses the real account
        // rather than the "details to follow" line it carried before.
        $this->assertStringContainsString('Donation', $body);

        foreach (array_filter(config('rcmaa.donation.bank')) as $field => $value) {
            $this->assertStringContainsString(
                e($value), $body, "The donation panel is missing its bank {$field}."
            );
        }

        // And the placeholder it replaced must be gone.
        $this->assertStringNotContainsString('শীঘ্রই জানানো হবে', $body);

        // It must appear alongside the account, above the transaction fields.
        $notice = strpos($body, 'ডোনেশন');
        $trxField = strpos($body, 'name="transaction_id"');
        $this->assertNotFalse($notice);
        $this->assertLessThan($trxField, $notice, 'The notice must be read before paying.');
    }
}
