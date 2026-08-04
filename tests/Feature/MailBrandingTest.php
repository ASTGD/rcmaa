<?php

namespace Tests\Feature;

use App\Mail\RegistrationReceived;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MailBrandingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Laravel's mail layout takes its header and copyright from APP_NAME. Left
     * at the default, every confirmation email goes out branded "Laravel".
     */
    #[Test]
    public function the_confirmation_email_is_branded_for_the_association(): void
    {
        $registration = Registration::create([
            'full_name_en' => 'Rasel Rana',
            'mobile' => '01712345678',
            'email' => 'rasel@example.com',
            'present_address' => 'Rajshahi',
            'session' => '2022-23',
            'degree' => 'msc',
            'passing_year' => 2024,
            'employment_status' => 'student_other',
            'tshirt_size' => 'L',
            'cultural_program' => true,
            'guest_count' => '0',
            'guests' => [],
            'payment_method' => 'bkash',
            'transaction_id' => 'ABC123',
            'sender_number' => '01712345678',
            'amount_paid' => 2000,
            'amount_due' => 2000,
        ]);

        $body = (new RegistrationReceived($registration))->render();

        $this->assertStringNotContainsStringIgnoringCase('Laravel', $body);
        $this->assertStringContainsString('RCMAA', $body);
        $this->assertStringContainsString($registration->reference, $body);
    }
}
