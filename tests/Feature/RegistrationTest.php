<?php

namespace Tests\Feature;

use App\Mail\RegistrationReceived;
use App\Models\Registration;
use App\Support\RegistrationPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** A complete, valid submission — individual tests override single keys. */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category' => 'alumni',
            'full_name_en' => 'Md. Rofikul Islam',
            'full_name_bn' => 'মোঃ রফিকুল ইসলাম',
            'blood_group' => 'B+',
            'mobile' => '01712345678',
            'whatsapp' => '01712345678',
            'email' => 'rofikul@example.com',
            'password' => 'reunion2026',
            'password_confirmation' => 'reunion2026',
            'present_address' => 'House 12, Road 4, Rajshahi',
            'permanent_address' => 'Village Char, Natore',
            'session' => '2008-09',
            'degree' => 'both',
            'masters_session' => '2012-13',
            'class_roll' => '4412',
            'registration_no' => '990122',
            'passing_year' => 2012,
            'employment_status' => 'employed',
            'profession' => 'Education',
            'designation' => 'Assistant Professor',
            'organization' => 'Rajshahi College',
            'tshirt_size' => 'XL',
            'cultural_program' => '1',
            'guest_count' => '0',
            'guests' => [],
            'memories' => 'The third-floor classroom and the afternoon calculus tutorials.',
            'payment_method' => 'bkash',
            'transaction_id' => 'A1B2C3D4E5',
            'sender_number' => '01712345678',
            'amount_paid' => RegistrationPricing::fee('alumni'),
            'terms' => '1',
        ], $overrides);
    }

    #[Test]
    public function it_shows_the_registration_form(): void
    {
        $this->get(route('register.create'))
            ->assertOk()
            ->assertSee('Reunion Registration')
            ->assertSee('ব্যক্তিগত তথ্য', false);
    }

    #[Test]
    public function it_stores_a_valid_registration_and_redirects_to_the_confirmation(): void
    {
        Mail::fake();

        $response = $this->post(route('register.store'), $this->payload());

        $registration = Registration::sole();

        $response->assertRedirect(route('register.confirmation', $registration->reference));

        $this->assertSame('Md. Rofikul Islam', $registration->full_name_en);
        $this->assertSame(Registration::STATUS_PENDING, $registration->payment_status);
        $this->assertStringStartsWith('RC26-', $registration->reference);
        $this->assertSame(RegistrationPricing::fee('alumni'), $registration->amount_due);
        $this->assertTrue($registration->cultural_program);

        Mail::assertSent(RegistrationReceived::class);
    }

    #[Test]
    public function it_stores_accompanying_guests_and_charges_for_them(): void
    {
        Mail::fake();

        $this->post(route('register.store'), $this->payload([
            'guest_count' => '2',
            'guests' => [
                ['name' => 'Shirin Akter', 'relation' => 'Spouse', 'occupation' => 'Teacher'],
                ['name' => 'Arif Islam', 'relation' => 'Son', 'occupation' => 'Student'],
            ],
            'amount_paid' => RegistrationPricing::total('alumni', 2),
        ]))->assertSessionHasNoErrors();

        $registration = Registration::sole();

        $this->assertCount(2, $registration->guests);
        $this->assertSame(2, $registration->guest_total);
        $this->assertSame(RegistrationPricing::total('alumni', 2), $registration->amount_due);
    }

    #[Test]
    public function it_rejects_a_payment_short_of_the_total_due(): void
    {
        $this->post(route('register.store'), $this->payload(['amount_paid' => 100]))
            ->assertSessionHasErrors('amount_paid');

        $this->assertSame(0, Registration::count());
    }

    #[Test]
    public function it_rejects_a_guest_count_that_disagrees_with_the_details_supplied(): void
    {
        $this->post(route('register.store'), $this->payload([
            'guest_count' => '2',
            'guests' => [['name' => 'Shirin Akter', 'relation' => 'Spouse', 'occupation' => 'Teacher']],
        ]))->assertSessionHasErrors('guests');
    }

    #[Test]
    public function it_rejects_a_duplicate_transaction_id_for_the_same_method(): void
    {
        Mail::fake();

        $this->post(route('register.store'), $this->payload())->assertSessionHasNoErrors();

        $this->post(route('register.store'), $this->payload([
            'email' => 'someone.else@example.com',
            'mobile' => '01812345678',
        ]))->assertSessionHasErrors('transaction_id');

        $this->assertSame(1, Registration::count());
    }

    #[Test]
    public function it_rejects_an_invalid_bangladeshi_mobile_number(): void
    {
        $this->post(route('register.store'), $this->payload(['mobile' => '12345']))
            ->assertSessionHasErrors('mobile');
    }

    #[Test]
    public function it_requires_employment_details_only_when_the_registrant_works(): void
    {
        $this->post(route('register.store'), $this->payload([
            'profession' => '',
            'organization' => '',
        ]))->assertSessionHasErrors(['profession', 'organization']);

        Mail::fake();

        $this->post(route('register.store'), $this->payload([
            'employment_status' => 'student_other',
            'profession' => '',
            'designation' => '',
            'organization' => '',
        ]))->assertSessionHasNoErrors();
    }

    #[Test]
    public function it_stores_an_uploaded_passport_photo(): void
    {
        Mail::fake();
        Storage::fake('public');

        $this->post(route('register.store'), $this->payload([
            'photo' => UploadedFile::fake()->image('passport.jpg', 400, 500),
        ]))->assertSessionHasNoErrors();

        $registration = Registration::sole();

        $this->assertNotNull($registration->photo_path);
        Storage::disk('public')->assertExists($registration->photo_path);
    }

    #[Test]
    public function it_rejects_a_photo_larger_than_one_megabyte(): void
    {
        Storage::fake('public');

        $this->post(route('register.store'), $this->payload([
            'photo' => UploadedFile::fake()->image('passport.jpg', 400, 500)->size(1500),
        ]))->assertSessionHasErrors('photo');
    }

    #[Test]
    public function it_rejects_submissions_that_fill_the_honeypot(): void
    {
        $this->post(route('register.store'), $this->payload(['website' => 'https://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Registration::count());
    }

    #[Test]
    public function it_refuses_registrations_while_registration_is_closed(): void
    {
        config(['rcmaa.registration.open' => false]);

        $this->post(route('register.store'), $this->payload())->assertForbidden();
    }

    #[Test]
    public function it_finds_a_registration_by_reference_and_mobile(): void
    {
        Mail::fake();
        $this->post(route('register.store'), $this->payload());
        $registration = Registration::sole();

        $this->post(route('registration.status.lookup'), [
            'reference' => strtolower($registration->reference),
            'mobile' => '01712345678',
        ])->assertOk()->assertSee($registration->reference);
    }

    #[Test]
    public function it_does_not_reveal_a_registration_when_the_mobile_does_not_match(): void
    {
        Mail::fake();
        $this->post(route('register.store'), $this->payload());
        $registration = Registration::sole();

        $this->post(route('registration.status.lookup'), [
            'reference' => $registration->reference,
            'mobile' => '01999999999',
        ])->assertOk()->assertSee('No registration found');
    }
}
