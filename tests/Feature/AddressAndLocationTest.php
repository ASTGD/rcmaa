<?php

namespace Tests\Feature;

use App\Models\Notice;
use App\Models\Registration;
use App\Support\RegistrationPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The structured address (8 Aug 2026 requirements): district and upazila/thana
 * dropdowns on the form, and the directory's place / passing-year filters
 * built on them.
 */
class AddressAndLocationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category' => 'alumni',
            'full_name_en' => 'Location Test',
            'mobile' => '01712345678',
            'email' => 'geo@example.test',
            'password' => 'reunion2026',
            'password_confirmation' => 'reunion2026',
            'present_address' => 'House 1, Boalia',
            'present_district' => 'Rajshahi',
            'present_upazila' => 'Rajshahi City Corporation (মহানগর)',
            'session' => '2008-09',
            'degree' => 'bsc',
            'passing_year' => 2012,
            'employment_status' => 'employed', 'work_location' => 'Rajshahi',
            'profession' => 'Education',
            'organization' => 'Rajshahi College',
            'tshirt_size' => 'L',
            'cultural_program' => '0',
            'guest_count' => '0',
            'guests' => [],
            'photo' => UploadedFile::fake()->image('portrait.jpg', 400, 500),
            'payment_method' => 'bkash',
            'transaction_id' => 'GEOTEST001',
            'sender_number' => '01712345678',
            'amount_paid' => RegistrationPricing::fee('alumni'),
            'terms' => '1',
        ], $overrides);
    }

    #[Test]
    public function the_dataset_covers_all_sixty_four_districts(): void
    {
        $this->assertCount(64, config('bd-geo'));
        // The department's own district must carry its city thana entry.
        $this->assertContains('Rajshahi City Corporation (মহানগর)', config('bd-geo')['Rajshahi']);
    }

    #[Test]
    public function a_registration_stores_its_structured_location(): void
    {
        $this->post(route('register.store'), $this->payload([
            'permanent_district' => 'Natore',
            'permanent_upazila' => 'Lalpur',
            'permanent_address' => 'Village home',
        ]))->assertRedirect()->assertSessionHasNoErrors();

        $r = Registration::firstOrFail();
        $this->assertSame('Rajshahi', $r->present_district);
        $this->assertSame('Rajshahi City Corporation (মহানগর)', $r->present_upazila);
        $this->assertSame('Natore', $r->permanent_district);
    }

    #[Test]
    public function an_upazila_must_belong_to_its_district(): void
    {
        // Savar is real — but it is in Dhaka, not Rajshahi.
        $this->post(route('register.store'), $this->payload(['present_upazila' => 'Savar']))
            ->assertSessionHasErrors('present_upazila');

        $this->assertSame(0, Registration::count());
    }

    #[Test]
    public function the_present_district_is_required(): void
    {
        $this->post(route('register.store'), $this->payload(['present_district' => '']))
            ->assertSessionHasErrors(['present_district']);
    }

    #[Test]
    public function memories_beyond_180_characters_are_refused(): void
    {
        $this->post(route('register.store'), $this->payload(['memories' => str_repeat('স', 181)]))
            ->assertSessionHasErrors('memories');

        $this->post(route('register.store'), $this->payload(['memories' => str_repeat('a', 180)]))
            ->assertRedirect()->assertSessionHasNoErrors();
    }

    #[Test]
    public function the_photo_is_mandatory(): void
    {
        $payload = $this->payload();
        unset($payload['photo']);

        $this->post(route('register.store'), $payload)->assertSessionHasErrors('photo');
    }

    #[Test]
    public function members_can_filter_the_directory_by_place_and_passing_year(): void
    {
        foreach ([
            ['Rajshahi Person', 'rp@example.test', 'T1', 'Rajshahi', 'Paba', 2010],
            ['Dhaka Person', 'dp@example.test', 'T2', 'Dhaka', 'Savar', 2015],
        ] as [$name, $email, $trx, $district, $upazila, $year]) {
            Registration::create([
                'category' => 'alumni', 'category_fee' => 2535, 'guest_fee' => 0,
                'full_name_en' => $name, 'mobile' => '01712345678', 'email' => $email,
                'present_address' => 'x', 'present_district' => $district, 'present_upazila' => $upazila,
                'session' => '2008-09', 'degree' => 'bsc', 'passing_year' => $year,
                'tshirt_size' => 'L', 'cultural_program' => false, 'guest_count' => '0', 'guests' => [],
                'payment_method' => 'bkash', 'transaction_id' => $trx, 'sender_number' => 'x',
                'amount_paid' => 2535, 'amount_due' => 2535,
                'payment_status' => Registration::STATUS_VERIFIED, 'listed_in_directory' => true,
            ]);
        }

        // Sign in as a member so the directory unlocks.
        $this->post(route('member.login'), ['email' => 'rp@example.test', 'password' => 'x']);
        $member = Registration::where('email', 'rp@example.test')->first();
        $member->update(['password' => 'reunion2026']);
        $this->post(route('member.login'), ['email' => 'rp@example.test', 'password' => 'reunion2026']);

        $this->get(route('directory', ['district' => 'Rajshahi']))
            ->assertOk()->assertSee('Rajshahi Person')->assertDontSee('Dhaka Person');

        $this->get(route('directory', ['passing_year' => 2015]))
            ->assertOk()->assertSee('Dhaka Person')->assertDontSee('Rajshahi Person');
    }

    #[Test]
    public function the_home_page_carries_the_notice_ticker(): void
    {
        Notice::create([
            'title' => 'Ticker check notice', 'slug' => 'ticker-check',
            'published_on' => now(), 'is_published' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-marquee', false)
            ->assertSee('Ticker check notice');
    }
}
