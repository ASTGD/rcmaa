<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The alumni directory is members-only, as the association's own FAQ states:
 * "শুধুমাত্র RCMAA-এর নিবন্ধিত সদস্যরাই লগইন করার মাধ্যমে Alumni Database দেখতে
 * পারবেন". It lists mobile numbers, so a public page would have broken a
 * promise alumni were relying on when they agreed to be listed.
 */
class DirectoryAccessTest extends TestCase
{
    use RefreshDatabase;

    private function member(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'category' => 'alumni', 'category_fee' => 2535, 'guest_fee' => 500,
            'full_name_en' => 'Listed Alumnus', 'mobile' => '01712345678',
            'email' => 'listed@example.test', 'present_address' => 'Rajshahi',
            'session' => '2008-09', 'degree' => 'bsc', 'passing_year' => 2012,
            'employment_status' => 'employed', 'profession' => 'Education',
            'tshirt_size' => 'L', 'cultural_program' => false,
            'guest_count' => '0', 'guests' => [], 'payment_method' => 'bkash',
            'transaction_id' => 'DIRTEST0001', 'sender_number' => '01712345678',
            'amount_paid' => 2535, 'amount_due' => 2535,
            'payment_status' => Registration::STATUS_VERIFIED,
            'listed_in_directory' => true,
        ], $overrides));
    }

    private function openPortal(Registration $r): void
    {
        $this->get(URL::temporarySignedRoute(
            'portal.open', now()->addHour(), ['registration' => $r->reference]
        ));
    }

    #[Test]
    public function a_stranger_is_sent_to_request_a_link(): void
    {
        $this->member();

        $this->get(route('directory'))
            ->assertRedirect(route('portal.request'))
            ->assertSessionHas('directory_gate');
    }

    /** The whole point: no mobile numbers leak to the public. */
    #[Test]
    public function no_alumni_details_are_served_to_the_public(): void
    {
        $this->member();

        $body = $this->get(route('directory'))->getContent();

        $this->assertStringNotContainsString('Listed Alumnus', $body);
        $this->assertStringNotContainsString('01712345678', $body);
    }

    #[Test]
    public function a_registrant_who_opened_their_link_can_view_it(): void
    {
        $r = $this->member();
        $this->openPortal($r);

        $this->get(route('directory'))
            ->assertOk()
            ->assertSee('Listed Alumnus')
            ->assertSee('01712345678');
    }

    #[Test]
    public function a_committee_member_can_view_it(): void
    {
        $this->member();

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('directory'))
            ->assertOk()
            ->assertSee('Listed Alumnus');
    }

    /** A signed-in non-admin user is not a member of the directory. */
    #[Test]
    public function a_non_admin_account_is_not_enough(): void
    {
        $this->member();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('directory'))
            ->assertRedirect(route('portal.request'));
    }

    #[Test]
    public function signing_out_of_the_portal_closes_the_directory_again(): void
    {
        $r = $this->member();
        $this->openPortal($r);
        $this->get(route('directory'))->assertOk();

        $this->post(route('portal.close'));

        $this->get(route('directory'))->assertRedirect(route('portal.request'));
    }

    #[Test]
    public function the_redirect_explains_why(): void
    {
        $this->get(route('directory'));

        $this->followingRedirects()->get(route('directory'))
            ->assertOk()
            ->assertSee('Members only')
            ->assertSee('অ্যালামনাই ডিরেক্টরি শুধুমাত্র RCMAA-এর নিবন্ধিত সদস্যদের জন্য', false);
    }

    /**
     * The home panel stays public at the association's request, but it shows
     * only name, session and profession — never a contact detail.
     */
    #[Test]
    public function the_home_panel_shows_no_contact_details(): void
    {
        $this->member();

        $body = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('Listed Alumnus', $body);
        $this->assertStringNotContainsString('01712345678', $body);
        $this->assertStringNotContainsString('listed@example.test', $body);
    }
}
