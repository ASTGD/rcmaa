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
            'email' => 'listed@example.test', 'present_address' => 'Rajshahi', 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
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
            'member.link.open', now()->addHour(), ['registration' => $r->reference]
        ));
    }

    /**
     * A guest is not turned away, but sees only how many members there are —
     * the association asked for exactly that much and no more.
     */
    #[Test]
    public function a_stranger_sees_only_the_member_count(): void
    {
        $this->member();

        $this->get(route('directory'))
            ->assertOk()
            ->assertSee('Members only')
            ->assertSee('Sign in to view')
            ->assertDontSee('Listed Alumnus');
    }

    #[Test]
    public function the_count_a_guest_sees_is_the_real_one(): void
    {
        $this->member();
        $this->member(['email' => 'second@example.test', 'transaction_id' => 'DIRTEST0002',
            'full_name_en' => 'Another Alumnus', 'session' => '2010-11']);
        // Unlisted people are not members of the public count either way.
        $this->member(['email' => 'third@example.test', 'transaction_id' => 'DIRTEST0003',
            'full_name_en' => 'Hidden Alumnus', 'listed_in_directory' => false]);

        $this->get(route('directory'))
            ->assertOk()
            ->assertSee('data-count="2"', false);
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
            ->assertOk()
            ->assertDontSee('Listed Alumnus');
    }

    #[Test]
    public function signing_out_closes_the_directory_again(): void
    {
        $r = $this->member();
        $this->openPortal($r);
        $this->get(route('directory'))->assertOk()->assertSee('Listed Alumnus');

        $this->post(route('member.logout'));

        $this->get(route('directory'))->assertOk()->assertDontSee('Listed Alumnus');
    }

    /** Members can narrow the directory the three ways the association asked for. */
    #[Test]
    public function a_signed_in_member_can_filter_by_name_session_and_category(): void
    {
        $r = $this->member();
        $this->member([
            'email' => 'teacher@example.test', 'transaction_id' => 'DIRTEST0009',
            'full_name_en' => 'Departmental Teacher', 'category' => 'teacher',
            'session' => null, 'degree' => null, 'passing_year' => null,
        ]);
        $this->openPortal($r);

        $this->get(route('directory', ['q' => 'Departmental']))
            ->assertOk()->assertSee('Departmental Teacher')->assertDontSee('Listed Alumnus');

        $this->get(route('directory', ['session' => '2008-09']))
            ->assertOk()->assertSee('Listed Alumnus')->assertDontSee('Departmental Teacher');

        $this->get(route('directory', ['category' => 'teacher']))
            ->assertOk()->assertSee('Departmental Teacher')->assertDontSee('Listed Alumnus');
    }

    #[Test]
    public function the_locked_page_explains_why_and_offers_a_way_in(): void
    {
        $this->get(route('directory'))
            ->assertOk()
            ->assertSee('Members only')
            ->assertSee('শুধুমাত্র RCMAA-এর নিবন্ধিত সদস্যরাই', false)
            ->assertSee(route('member.login'), false)
            ->assertSee(route('register.create'), false);
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
