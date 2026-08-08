<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Notifications\MemberPasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The member account the association specified: a password set during
 * registration, an email-and-password login, a reset by email, and a dashboard
 * that hands back the two slips as PDFs.
 */
class MemberAccountTest extends TestCase
{
    use RefreshDatabase;

    private function member(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'category' => 'alumni', 'category_fee' => 2535, 'guest_fee' => 0,
            'full_name_en' => 'Md. Rofikul Islam', 'mobile' => '01712345678',
            'email' => 'rofikul@example.com', 'password' => 'reunion2026',
            'present_address' => 'Rajshahi', 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba', 'session' => '2008-09',
            'degree' => 'bsc', 'passing_year' => 2012,
            'employment_status' => 'employed', 'profession' => 'Education',
            'organization' => 'Rajshahi College', 'tshirt_size' => 'XL',
            'cultural_program' => false, 'guest_count' => '0', 'guests' => [],
            'payment_method' => 'bkash', 'transaction_id' => 'MEMBER0001',
            'sender_number' => '01712345678', 'amount_paid' => 2535,
            'amount_due' => 2535, 'payment_status' => Registration::STATUS_VERIFIED,
        ], $overrides));
    }

    /* ---------------------------------------------------------------- login */

    #[Test]
    public function a_member_signs_in_with_their_email_and_password(): void
    {
        $member = $this->member();

        $this->post(route('member.login.attempt'), [
            'email' => 'rofikul@example.com',
            'password' => 'reunion2026',
        ])->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticatedAs($member, 'alumni');
        $this->assertNotNull($member->fresh()->last_login_at);
    }

    #[Test]
    public function a_wrong_password_is_refused(): void
    {
        $this->member();

        $this->post(route('member.login.attempt'), [
            'email' => 'rofikul@example.com',
            'password' => 'not-the-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('alumni');
    }

    /** The password is never stored in the clear, whatever else happens. */
    #[Test]
    public function the_password_is_hashed_at_rest_and_hidden_from_serialisation(): void
    {
        $member = $this->member();

        $this->assertNotSame('reunion2026', $member->getAuthPassword());
        $this->assertStringStartsWith('$', $member->getAuthPassword());
        $this->assertArrayNotHasKey('password', $member->toArray());
        $this->assertArrayNotHasKey('remember_token', $member->toArray());
    }

    /**
     * Anyone who registered before member accounts existed has no password and
     * cannot be left guessing at one.
     */
    #[Test]
    public function a_member_without_a_password_is_told_how_to_get_in(): void
    {
        $this->member(['password' => null]);

        $this->post(route('member.login.attempt'), [
            'email' => 'rofikul@example.com',
            'password' => 'anything',
        ])->assertSessionHasErrors('email');

        $this->assertStringContainsString(
            'Forgot password',
            session('errors')->first('email')
        );
    }

    #[Test]
    public function the_login_page_carries_everything_the_specification_asks_for(): void
    {
        $this->get(route('member.login'))
            ->assertOk()
            ->assertSee('Forgot password?')
            ->assertSee('Need help?')
            ->assertSee('Register here')
            ->assertSee(route('member.password.request'), false)
            ->assertSee(route('register.create'), false)
            // The support route the association asked to be visible here.
            ->assertSee(config('rcmaa.contact.helpline'));
    }

    #[Test]
    public function signing_out_ends_the_session(): void
    {
        $member = $this->member();

        $this->actingAs($member, 'alumni')->post(route('member.logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest('alumni');
    }

    /* ------------------------------------------------------- password reset */

    #[Test]
    public function a_reset_link_is_emailed_and_resets_the_password(): void
    {
        Notification::fake();
        $member = $this->member();

        $this->post(route('member.password.email'), ['email' => $member->email])
            ->assertRedirect()->assertSessionHas('status');

        Notification::assertSentTo($member, MemberPasswordReset::class);

        $token = Password::broker('alumni')->createToken($member);

        $this->post(route('member.password.update'), [
            'token' => $token,
            'email' => $member->email,
            'password' => 'brand-new-2026',
            'password_confirmation' => 'brand-new-2026',
        ])->assertRedirect(route('member.login'));

        $this->post(route('member.login.attempt'), [
            'email' => $member->email,
            'password' => 'brand-new-2026',
        ])->assertRedirect(route('member.dashboard'));
    }

    /** The reset form must not become a way to discover who has registered. */
    #[Test]
    public function an_unknown_address_gets_the_same_answer_and_no_email(): void
    {
        Notification::fake();

        $this->post(route('member.password.email'), ['email' => 'nobody@example.com'])
            ->assertRedirect()->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    #[Test]
    public function a_weak_password_is_refused_on_reset(): void
    {
        $member = $this->member();
        $token = Password::broker('alumni')->createToken($member);

        $this->post(route('member.password.update'), [
            'token' => $token,
            'email' => $member->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    #[Test]
    public function changing_a_password_requires_the_current_one(): void
    {
        $member = $this->member();

        $this->actingAs($member, 'alumni')->post(route('member.password.store'), [
            'current_password' => 'wrong',
            'password' => 'another-one-2026',
            'password_confirmation' => 'another-one-2026',
        ])->assertSessionHasErrors('current_password');

        // Setting a first password has nothing to prove, so it needs no current.
        $fresh = $this->member(['email' => 'nopass@example.com', 'password' => null,
            'transaction_id' => 'MEMBER0002']);

        $this->actingAs($fresh, 'alumni')->post(route('member.password.store'), [
            'password' => 'first-password-2026',
            'password_confirmation' => 'first-password-2026',
        ])->assertRedirect(route('member.dashboard'))->assertSessionHasNoErrors();

        $this->assertTrue($fresh->fresh()->hasPassword());
    }

    /* ------------------------------------------------------------ dashboard */

    #[Test]
    public function the_dashboard_is_closed_to_anyone_not_signed_in(): void
    {
        foreach (['member.dashboard', 'member.pass', 'member.slip.registration', 'member.slip.payment'] as $route) {
            $this->get(route($route))->assertRedirect(route('member.login'));
        }
    }

    #[Test]
    public function a_member_can_change_their_profile_picture(): void
    {
        Storage::fake('public');
        $member = $this->member();

        $this->actingAs($member, 'alumni')->patch(route('member.profile.update'), [
            'full_name_en' => $member->full_name_en,
            'mobile' => $member->mobile,
            'present_address' => $member->present_address, 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
            'tshirt_size' => 'L',
            'photo' => UploadedFile::fake()->image('me.jpg', 400, 500),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $path = $member->fresh()->photo_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    /** Replacing a picture should not leave the old file behind. */
    #[Test]
    public function replacing_the_picture_removes_the_previous_file(): void
    {
        Storage::fake('public');
        $member = $this->member();

        $send = fn (string $name) => $this->actingAs($member, 'alumni')
            ->patch(route('member.profile.update'), [
                'full_name_en' => $member->full_name_en,
                'mobile' => $member->mobile,
                'present_address' => $member->present_address, 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
                'tshirt_size' => $member->tshirt_size,
                'photo' => UploadedFile::fake()->image($name, 400, 500),
            ]);

        $send('first.jpg');
        $first = $member->fresh()->photo_path;

        $send('second.jpg');
        $second = $member->fresh()->photo_path;

        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);
    }

    #[Test]
    public function a_member_can_update_their_workplace_and_shirt_size(): void
    {
        $member = $this->member();

        $this->actingAs($member, 'alumni')->patch(route('member.profile.update'), [
            'full_name_en' => $member->full_name_en,
            'mobile' => '01799999999',
            'present_address' => $member->present_address, 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
            'tshirt_size' => 'M',
            'employment_status' => 'self_employed',
            'profession' => 'Software',
            'designation' => 'Director',
            'organization' => 'A Company Ltd',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $member->refresh();
        $this->assertSame('M', $member->tshirt_size);
        $this->assertSame('self_employed', $member->employment_status);
        $this->assertSame('A Company Ltd', $member->organization);
        $this->assertSame('01799999999', $member->mobile);
    }

    /* ---------------------------------------------------------------- slips */

    #[Test]
    public function the_registration_slip_downloads_as_a_pdf(): void
    {
        $member = $this->member();

        $response = $this->actingAs($member, 'alumni')->get(route('member.slip.registration'));

        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            "RCMAA-registration-{$member->reference}.pdf",
            $response->headers->get('content-disposition')
        );
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    #[Test]
    public function the_payment_slip_downloads_as_a_pdf(): void
    {
        $member = $this->member();

        $response = $this->actingAs($member, 'alumni')->get(route('member.slip.payment'));

        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            "RCMAA-payment-{$member->reference}.pdf",
            $response->headers->get('content-disposition')
        );
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    /** One member must never be able to fetch another's slip. */
    #[Test]
    public function a_slip_only_ever_carries_the_signed_in_member(): void
    {
        $mine = $this->member();
        $other = $this->member([
            'email' => 'someone.else@example.com', 'transaction_id' => 'MEMBER0003',
            'full_name_en' => 'Someone Else',
        ]);

        $pdf = $this->actingAs($mine, 'alumni')
            ->get(route('member.slip.registration'))->getContent();

        $this->assertStringNotContainsString($other->reference, $pdf);
    }
}
