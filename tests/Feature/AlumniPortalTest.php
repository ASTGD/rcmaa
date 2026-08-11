<?php

namespace Tests\Feature;

use App\Mail\AlumniAccessLink;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The member's own area.
 *
 * Members sign in with an email and password on the `alumni` guard. The signed,
 * expiring link is kept for anyone who registered before member accounts
 * existed, and lands them on "choose a password" rather than the dashboard.
 * The tests that matter most are the ones about who can get in at all and what
 * the area refuses to change.
 */
class AlumniPortalTest extends TestCase
{
    use RefreshDatabase;

    /** The directory is members-only; these checks view it as the committee. */
    private function viewingDirectory(): self
    {
        return $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'category' => 'alumni',
            'category_fee' => 2535,
            'guest_fee' => 500,
            'full_name_en' => 'Md. Rofikul Islam',
            'mobile' => '01712345678',
            'email' => 'rofikul@example.com',
            'password' => 'reunion2026',
            'present_address' => 'Rajshahi', 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
            'session' => '2008-09',
            'degree' => 'both',
            'masters_session' => '2012-13',
            'passing_year' => 2012,
            'employment_status' => 'employed',
            'profession' => 'Education',
            'organization' => 'Rajshahi College',
            'tshirt_size' => 'XL',
            'cultural_program' => true,
            'guest_count' => '0',
            'guests' => [],
            'payment_method' => 'bkash',
            'transaction_id' => 'A1B2C3D4E5',
            'sender_number' => '01712345678',
            'amount_paid' => 2535,
            'amount_due' => 2535,
            'payment_status' => Registration::STATUS_VERIFIED,
        ], $overrides));
    }

    private function open(Registration $r): void
    {
        $this->get(URL::temporarySignedRoute('member.link.open', now()->addHour(), ['registration' => $r->reference]))
            ->assertRedirect(route('member.dashboard'));
    }

    /** Signed in as this member, without going through a form. */
    private function asMember(Registration $r): self
    {
        return $this->actingAs($r, 'alumni');
    }

    #[Test]
    public function it_emails_a_link_to_a_registered_address(): void
    {
        Mail::fake();
        $r = $this->registration();

        $this->post(route('member.link.send'), ['email' => $r->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertSent(AlumniAccessLink::class, fn ($m) => $m->hasTo($r->email));
    }

    /** The form must not become a way to discover who has registered. */
    #[Test]
    public function an_unknown_address_gets_the_same_answer_and_no_email(): void
    {
        Mail::fake();

        $this->post(route('member.link.send'), ['email' => 'nobody@example.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertNothingSent();
    }

    #[Test]
    public function the_area_is_closed_without_a_valid_link(): void
    {
        $this->get(route('member.dashboard'))->assertRedirect(route('member.login'));
        $this->get(route('member.pass'))->assertRedirect(route('member.login'));
    }

    #[Test]
    public function a_tampered_or_expired_link_is_refused(): void
    {
        $r = $this->registration();

        // Tampered signature.
        $url = URL::temporarySignedRoute('member.link.open', now()->addHour(), ['registration' => $r->reference]);
        $this->get($url.'tamper')->assertForbidden();

        // Expired.
        $expired = URL::temporarySignedRoute('member.link.open', now()->subMinute(), ['registration' => $r->reference]);
        $this->get($expired)->assertForbidden();
    }

    #[Test]
    public function a_valid_link_opens_the_registration(): void
    {
        $r = $this->registration();
        $this->open($r);

        $this->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee($r->reference)
            ->assertSee('Md. Rofikul Islam');
    }

    #[Test]
    public function the_registrant_can_correct_their_own_contact_details(): void
    {
        $r = $this->registration();
        $this->open($r);

        $this->patch(route('member.profile.update'), [
            'full_name_en' => $r->full_name_en,
            'mobile' => '01799999999',
            'present_address' => 'New address, Rajshahi',
            'tshirt_size' => 'M',
            'employment_status' => 'employed',
            'listed_in_directory' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $r->refresh();
        $this->assertSame('01799999999', $r->mobile);
        $this->assertSame('M', $r->tshirt_size);
    }

    /**
     * Money, academic identity and the email itself stay the committee's.
     *
     * The name is deliberately not in that list any more — the association
     * asked for members to be able to correct their own. Everything the
     * committee verifies against is still refused.
     */
    #[Test]
    public function the_registrant_cannot_change_money_academic_identity_or_their_email(): void
    {
        $r = $this->registration();
        $this->open($r);

        $this->patch(route('member.profile.update'), [
            'full_name_en' => 'Md. Rofikul Islam',
            'mobile' => $r->mobile,
            'present_address' => $r->present_address, 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
            'tshirt_size' => $r->tshirt_size,
            'listed_in_directory' => '1',
            // All of these should be ignored.
            'category' => 'teacher', 'teacher_type' => 'teacher',
            'amount_paid' => 999999,
            'amount_due' => 0,
            'payment_status' => 'verified',
            'session' => '1999-00',
            'passing_year' => 1999,
            'email' => 'attacker@example.com',
            'transaction_id' => 'HIJACKED',
            'reference' => 'RCMAA-HACK',
        ])->assertRedirect();

        $r->refresh();
        $this->assertSame('alumni', $r->category);
        $this->assertSame(2535, $r->amount_paid);
        $this->assertSame('2008-09', $r->session);
        $this->assertSame(2012, $r->passing_year);
        $this->assertSame('rofikul@example.com', $r->email);
        $this->assertSame('A1B2C3D4E5', $r->transaction_id);
        $this->assertSame(Registration::STATUS_VERIFIED, $r->payment_status);
    }

    /** The one identity field that is now theirs. */
    #[Test]
    public function the_registrant_can_correct_their_own_name(): void
    {
        $r = $this->registration();
        $this->open($r);

        $this->patch(route('member.profile.update'), [
            'full_name_en' => 'Md. Rofiqul Islam',
            'full_name_bn' => 'মোঃ রফিকুল ইসলাম',
            'mobile' => $r->mobile,
            'present_address' => $r->present_address, 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
            'tshirt_size' => $r->tshirt_size,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame('Md. Rofiqul Islam', $r->fresh()->full_name_en);
    }

    #[Test]
    public function the_registrant_can_remove_themselves_from_the_directory(): void
    {
        $r = $this->registration(['full_name_en' => 'Wants Privacy']);
        $this->viewingDirectory()->get(route('directory'))->assertOk()->assertSee('Wants Privacy');

        $this->open($r);
        $this->patch(route('member.profile.update'), [
            'full_name_en' => $r->full_name_en,
            'mobile' => $r->mobile,
            'present_address' => $r->present_address, 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
            'tshirt_size' => $r->tshirt_size,
            // checkbox omitted = opted out
        ])->assertRedirect();

        $this->assertFalse($r->fresh()->listed_in_directory);
        $this->assertSame(Registration::STATUS_VERIFIED, $r->fresh()->payment_status);
        $this->viewingDirectory()->get(route('directory'))->assertOk()->assertDontSee('Wants Privacy');
    }

    #[Test]
    public function the_entry_pass_renders_with_the_details_the_desk_needs(): void
    {
        $r = $this->registration([
            'guest_count' => '1',
            'guests' => [['name' => 'Shirin Akter', 'relation' => 'Spouse', 'occupation' => 'Teacher']],
        ]);
        $this->open($r);

        // The pass is now a PDF download rather than a page, so it is checked
        // the way the registration and payment slips are: the right file, and
        // the details rendered into it read back out of the PDF's own text.
        $response = $this->get(route('member.pass'));

        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            "RCMAA-pass-{$r->reference}.pdf",
            $response->headers->get('content-disposition')
        );

        $text = $this->pdfText($response->getContent());
        foreach ([$r->reference, 'Md. Rofikul Islam', 'Alumnus', 'XL', 'Shirin Akter'] as $needle) {
            $this->assertStringContainsString($needle, $text, "The pass PDF is missing: {$needle}");
        }
    }

    #[Test]
    public function an_unverified_pass_is_blocked(): void
    {
        $r = $this->registration(['payment_status' => Registration::STATUS_PENDING]);
        $this->open($r);

        $this->get(route('member.pass'))->assertStatus(403);
    }

    #[Test]
    public function signing_out_closes_the_session(): void
    {
        $r = $this->registration();
        $this->open($r);
        $this->get(route('member.dashboard'))->assertOk();

        $this->post(route('member.logout'))->assertRedirect(route('home'));
        $this->get(route('member.dashboard'))->assertRedirect(route('member.login'));
    }

    #[Test]
    public function the_link_request_form_rejects_the_honeypot(): void
    {
        Mail::fake();
        $r = $this->registration();

        $this->post(route('member.link.send'), ['email' => $r->email, 'website' => 'spam'])
            ->assertSessionHasErrors('website');

        Mail::assertNothingSent();
    }

    /**
     * The x-field component was written for the Alpine-backed registration
     * form, which holds its own state, so it only ever read old(). The portal
     * is the first plain server-rendered form to use it and painted every box
     * blank — a registrant opening the page saw an empty form over their real
     * record.
     */
    #[Test]
    public function the_edit_form_is_prefilled_with_the_stored_details(): void
    {
        $r = $this->registration([
            'whatsapp' => '01712345678',
            'blood_group' => 'B+',
            'permanent_address' => 'Natore',
            'designation' => 'Officer',
            'memories' => 'Afternoon tutorials on the third floor.',
        ]);
        $this->open($r);

        $body = $this->get(route('member.dashboard'))->assertOk()->getContent();

        // Text inputs and textareas.
        foreach (['mobile', 'whatsapp', 'present_address', 'permanent_address',
            'profession', 'designation', 'organization', 'memories'] as $field) {
            $this->assertStringContainsString(
                e($r->{$field}), $body, "Field rendered blank: {$field}"
            );
        }

        // Selects must carry the stored option, not just the placeholder.
        $this->assertStringContainsString('<option value="B+" selected>', $body);
        $this->assertStringContainsString('<option value="XL" selected>', $body);
    }

    /** The same component still leaves Alpine-backed forms to their own state. */
    #[Test]
    public function the_registration_form_is_not_prefilled_by_the_component(): void
    {
        $body = $this->get(route('register.create'))->assertOk()->getContent();

        $this->assertStringContainsString('x-model="form.full_name_en"', $body);
        // Alpine owns the value; the server must not seed it.
        $this->assertMatchesRegularExpression(
            '/name="full_name_en".{0,200}?value=""/s', $body,
            'The registration form should still render empty for Alpine to fill.'
        );
    }
}
