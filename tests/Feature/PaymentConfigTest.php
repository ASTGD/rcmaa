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

    #[Test]
    public function the_public_phone_number_is_the_association_helpline(): void
    {
        $this->assertSame('+880 1643-740416', config('rcmaa.contact.phone'));
        $this->assertSame('+880 1643-740416', config('rcmaa.contact.hotline'));

        $this->get(route('contact'))->assertOk()->assertSee('+880 1643-740416');
        $this->get(route('home'))->assertOk()->assertSee('+880 1643-740416');
    }
}
