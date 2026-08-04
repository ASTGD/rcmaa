<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function bkash_is_the_only_method_and_it_is_a_merchant_account(): void
    {
        $methods = config('rcmaa.payment.methods');

        $this->assertSame(['bkash'], array_keys($methods));
        $this->assertSame('01400366369', $methods['bkash']['number']);
        $this->assertSame('Merchant', $methods['bkash']['type']);
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
            'present_address' => 'Rajshahi', 'session' => '2008-09',
            'degree' => 'bsc', 'passing_year' => 2012,
            'employment_status' => 'employed', 'profession' => 'Education',
            'organization' => 'Rajshahi College', 'tshirt_size' => 'L',
            'cultural_program' => '0', 'guest_count' => '0',
            'transaction_id' => 'BKASHONLY1', 'sender_number' => '01712345678',
            'amount_paid' => 2535, 'terms' => '1',
        ];

        $this->post(route('register.store'), ['payment_method' => 'nagad'] + $payload)
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

        $this->assertStringContainsString('বিশেষ নির্দেশনা', $body);
        $this->assertStringContainsString('Donation/Contribution', $body);
        $this->assertStringContainsString('০১৪০০-৩৬৬৩৬৯', $body);
        $this->assertStringContainsString('tel:01400366369', $body);

        // It must appear alongside the account, above the transaction fields.
        $notice = strpos($body, 'বিশেষ নির্দেশনা');
        $trxField = strpos($body, 'name="transaction_id"');
        $this->assertNotFalse($notice);
        $this->assertLessThan($trxField, $notice, 'The notice must be read before paying.');
    }
}
