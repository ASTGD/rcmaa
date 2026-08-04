<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Two things the association asked for on the academic step.
 *
 * A current student has not passed yet, so a passing year cannot be demanded of
 * them. And "Session" alone is ambiguous for anyone who studied here twice —
 * the field now names which session it wants, and asks for a second one when
 * both degrees were taken here.
 */
class AcademicDetailsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category' => 'alumni',
            'full_name_en' => 'Md. Rofikul Islam',
            'mobile' => '01712345678',
            'email' => 'rofikul@example.test',
            'present_address' => 'Rajshahi',
            'session' => '2008-09',
            'degree' => 'bsc',
            'passing_year' => 2012,
            'employment_status' => 'employed',
            'profession' => 'Education',
            'organization' => 'Rajshahi College',
            'tshirt_size' => 'L',
            'cultural_program' => '0',
            'guest_count' => '0',
            'payment_method' => 'bkash',
            'transaction_id' => 'ACADEMIC01',
            'sender_number' => '01712345678',
            'amount_paid' => 2535,
            'terms' => '1',
        ], $overrides);
    }

    // --- Passing year ---------------------------------------------------

    #[Test]
    public function a_current_student_may_leave_the_passing_year_blank(): void
    {
        $this->post(route('register.store'), $this->payload([
            'category' => 'current_student',
            'session' => '2022-23',
            'employment_status' => 'student_other',
            'profession' => null,
            'organization' => null,
            'passing_year' => null,
            'amount_paid' => 1015,
        ]))->assertRedirect()->assertSessionHasNoErrors();

        $r = Registration::firstOrFail();
        $this->assertNull($r->passing_year);
        $this->assertSame('current_student', $r->category);
    }

    #[Test]
    public function everybody_else_must_still_give_one(): void
    {
        $this->post(route('register.store'), $this->payload(['passing_year' => null]))
            ->assertSessionHasErrors('passing_year');

        $this->assertSame(0, Registration::count());
    }

    #[Test]
    public function a_blank_passing_year_reads_as_still_studying(): void
    {
        $this->post(route('register.store'), $this->payload([
            'category' => 'current_student', 'session' => '2022-23',
            'employment_status' => 'student_other', 'profession' => null,
            'organization' => null, 'passing_year' => null, 'amount_paid' => 1015,
        ]));

        $r = Registration::firstOrFail();
        $this->get(route('register.confirmation', $r->reference))
            ->assertOk()
            ->assertSee('Still studying');
    }

    // --- Which session? -------------------------------------------------

    #[Test]
    public function the_session_field_names_the_degree_it_is_asking_about(): void
    {
        $body = $this->get(route('register.create'))->assertOk()->getContent();

        // One input per name — a hidden duplicate would still be submitted.
        $this->assertSame(1, substr_count($body, 'name="session"'));
        $this->assertSame(1, substr_count($body, 'name="masters_session"'));

        // The label is driven by the chosen degree.
        $this->assertStringContainsString('x-text="sessionLabel"', $body);
        $this->assertStringContainsString('x-text="sessionLabelBn"', $body);
        $this->assertStringContainsString('needsMastersSession', $body);
    }

    #[Test]
    public function both_degrees_means_two_sessions_are_recorded(): void
    {
        $this->post(route('register.store'), $this->payload([
            'degree' => 'both',
            'session' => '2008-09',
            'masters_session' => '2012-13',
        ]))->assertRedirect()->assertSessionHasNoErrors();

        $r = Registration::firstOrFail();
        $this->assertSame('2008-09', $r->session);
        $this->assertSame('2012-13', $r->masters_session);
    }

    #[Test]
    public function both_degrees_without_the_masters_session_is_refused(): void
    {
        $this->post(route('register.store'), $this->payload(['degree' => 'both']))
            ->assertSessionHasErrors('masters_session');

        $this->assertSame(0, Registration::count());
    }

    #[Test]
    public function a_single_degree_needs_no_second_session(): void
    {
        foreach (['bsc', 'msc', 'previous_masters'] as $i => $degree) {
            Registration::query()->delete();

            $this->post(route('register.store'), $this->payload([
                'degree' => $degree,
                'email' => "one{$i}@example.test",
                'transaction_id' => 'SINGLE'.$i.'XXXX',
            ]))->assertRedirect()->assertSessionHasNoErrors();

            $this->assertNull(Registration::firstOrFail()->masters_session, "{$degree} should need no Masters session");
        }
    }

    /** The Masters session is canonicalised exactly like the primary one. */
    #[Test]
    public function the_masters_session_is_stored_in_canonical_form(): void
    {
        $this->post(route('register.store'), $this->payload([
            'degree' => 'both',
            'session' => '2008-09',
            'masters_session' => '2012-13',
        ]));

        $r = Registration::firstOrFail();
        $r->masters_session = '২০১৪-১৫';
        $r->save();

        $this->assertSame('2014-15', $r->fresh()->masters_session);
    }

    /** Registration questions should reach the registration helpline. */
    #[Test]
    public function the_form_offers_the_registration_helpline(): void
    {
        $this->get(route('register.create'))
            ->assertOk()
            ->assertSee('01990168773')
            ->assertSee('tel:01990168773', false);
    }
}
