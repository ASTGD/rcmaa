<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The association asked for the directory batch by batch — "কোন ব্যাচের ছাত্র
 * পূরণ করেছে সেটা View Directory তে আলাদা আলাদা ভাবে Show করবে" — and for the
 * two most recent registrants on the home page.
 */
class DirectoryBatchTest extends TestCase
{
    use RefreshDatabase;

    private function alumnus(string $name, string $session, array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'category' => 'alumni', 'category_fee' => 2535, 'guest_fee' => 500,
            'full_name_en' => $name, 'mobile' => '01712345678',
            'email' => Str()->slug($name).'@example.test', 'present_address' => 'Rajshahi',
            'session' => $session, 'degree' => 'bsc',
            'passing_year' => (int) substr($session, 0, 4) + 4,
            'employment_status' => 'employed', 'profession' => 'Education',
            'tshirt_size' => 'L', 'cultural_program' => false,
            'guest_count' => '0', 'guests' => [], 'payment_method' => 'bkash',
            'transaction_id' => Str()->upper(Str()->random(10)), 'sender_number' => '01712345678',
            'amount_paid' => 2535, 'amount_due' => 2535,
            'payment_status' => Registration::STATUS_VERIFIED,
            'listed_in_directory' => true,
        ], $overrides));
    }

    /** Opens a portal session, which is what the directory now requires. */
    private function asMember(): self
    {
        return $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    /** Typed variants of one batch must not become several headings. */
    #[Test]
    public function session_spellings_collapse_to_one_batch(): void
    {
        $this->alumnus('Alpha One', '2008-09');
        $this->alumnus('Bravo Two', '2008-2009');
        $this->alumnus('Charlie Three', '২০০৮-০৯');

        $this->assertSame(
            ['2008-09'],
            Registration::listed()->distinct()->pluck('session')->all()
        );

        $body = $this->asMember()->get(route('directory'))->assertOk()->getContent();
        $this->assertSame(1, substr_count($body, 'Session 2008-09'));
    }

    #[Test]
    public function each_batch_gets_its_own_heading_with_a_count(): void
    {
        $this->alumnus('Alpha One', '2008-09');
        $this->alumnus('Bravo Two', '2008-09');
        $this->alumnus('Charlie Three', '2014-15');

        $this->asMember()->get(route('directory'))
            ->assertOk()
            ->assertSee('Session 2008-09')
            ->assertSee('Session 2014-15')
            ->assertSee('2 graduates')
            ->assertSee('1 graduate')
            ->assertSee('3 members across 2 groups')
            ->assertSee('Alpha One')
            ->assertSee('Charlie Three');
    }

    #[Test]
    public function batches_are_listed_newest_first(): void
    {
        $this->alumnus('Older Person', '2005-06');
        $this->alumnus('Newer Person', '2018-19');

        $body = $this->asMember()->get(route('directory'))->assertOk()->getContent();

        $this->assertLessThan(
            strpos($body, 'Session 2005-06'),
            strpos($body, 'Session 2018-19'),
            'The most recent batch should come first.'
        );
    }

    #[Test]
    public function a_batch_can_be_filtered_on_its_own(): void
    {
        $this->alumnus('Alpha One', '2008-09');
        $this->alumnus('Charlie Three', '2014-15');

        $this->asMember()->get(route('directory', ['session' => '2008-09']))
            ->assertOk()
            ->assertSee('Alpha One')
            ->assertDontSee('Charlie Three')
            ->assertSee('1 member across 1 group');
    }

    /** Pagination counts batches, so a batch is never split across pages. */
    #[Test]
    public function pagination_never_splits_a_batch(): void
    {
        foreach (['2001-02', '2002-03', '2003-04', '2004-05', '2005-06', '2006-07', '2007-08'] as $i => $session) {
            $this->alumnus("Person {$i}A", $session);
            $this->alumnus("Person {$i}B", $session);
        }

        // Seven batches, six per page.
        $first = $this->asMember()->get(route('directory'))->assertOk();
        $first->assertSee('Session 2007-08')->assertDontSee('Session 2001-02');

        $second = $this->asMember()->get(route('directory', ['page' => 2]))->assertOk();
        $second->assertSee('Session 2001-02')->assertSee('Person 0A')->assertSee('Person 0B');
    }

    #[Test]
    public function only_verified_and_opted_in_people_appear(): void
    {
        $this->alumnus('Shown Person', '2010-11');
        $this->alumnus('Unverified Person', '2010-11', ['payment_status' => Registration::STATUS_PENDING]);
        $this->alumnus('Opted Out Person', '2010-11', ['listed_in_directory' => false]);

        $this->asMember()->get(route('directory'))
            ->assertOk()
            ->assertSee('Shown Person')
            ->assertDontSee('Unverified Person')
            ->assertDontSee('Opted Out Person');
    }

    #[Test]
    public function the_jump_list_offers_every_batch(): void
    {
        $this->alumnus('Alpha One', '2008-09');
        $this->alumnus('Charlie Three', '2014-15');

        $this->asMember()->get(route('directory'))
            ->assertOk()
            ->assertSee('Jump to batch')
            ->assertSee(route('directory', ['session' => '2008-09']), false)
            ->assertSee(route('directory', ['session' => '2014-15']), false);
    }

    // --- Home page ------------------------------------------------------

    #[Test]
    public function the_home_page_shows_the_two_most_recent_registrants(): void
    {
        $this->alumnus('First Joiner', '2001-02');
        $this->alumnus('Second Joiner', '2005-06');
        $this->alumnus('Third Joiner', '2010-11');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Recently joined')
            ->assertSee('Third Joiner')
            ->assertSee('Second Joiner')
            ->assertDontSee('First Joiner')
            ->assertSee('3 listed');
    }

    #[Test]
    public function the_home_page_omits_the_panel_when_nobody_has_registered(): void
    {
        $this->get(route('home'))->assertOk()->assertDontSee('Recently joined');
    }

    #[Test]
    public function the_home_page_does_not_show_unverified_or_opted_out_people(): void
    {
        $this->alumnus('Pending Person', '2010-11', ['payment_status' => Registration::STATUS_PENDING]);
        $this->alumnus('Private Person', '2011-12', ['listed_in_directory' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Pending Person')
            ->assertDontSee('Private Person')
            ->assertDontSee('Recently joined');
    }

    /** The client's point 1: the home CTA opens the registration form. */
    #[Test]
    public function the_home_page_offers_join_the_association_and_view_directory(): void
    {
        $body = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('Join the Association', $body);
        $this->assertStringContainsString('href="'.route('register.create').'"', $body);
        $this->assertStringContainsString('View Directory', $body);
        $this->assertStringContainsString('href="'.route('directory').'"', $body);
    }

    // --- Session is chosen, not typed ------------------------------------

    /**
     * A free text box produced 2008-09, 2008-2009 and ২০০৮-০৯ for one cohort.
     * Batch grouping only holds together if the value is picked from a list.
     */
    #[Test]
    public function the_registration_form_offers_sessions_as_a_dropdown(): void
    {
        $body = $this->get(route('register.create'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/<select[^>]*name="session"/', $body,
            'Session must be a dropdown, not a text field.'
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<input[^>]*name="session"/', $body,
            'The old free text session input should be gone.'
        );

        foreach (['2025-26', '2014-15', '1999-00', '1950-51'] as $session) {
            $this->assertStringContainsString('value="'.$session.'"', $body, "Missing session: {$session}");
        }
    }

    #[Test]
    public function a_session_outside_the_list_is_refused(): void
    {
        $payload = [
            'category' => 'alumni', 'full_name_en' => 'Bad Session',
            'mobile' => '01712345678', 'email' => 'bad@example.test',
            'present_address' => 'Rajshahi', 'session' => '2008-2009',
            'degree' => 'bsc', 'passing_year' => 2012,
            'employment_status' => 'employed', 'profession' => 'Education',
            'organization' => 'Rajshahi College', 'tshirt_size' => 'L',
            'cultural_program' => '0', 'guest_count' => '0',
            'payment_method' => 'bkash', 'transaction_id' => 'BADSESSION',
            'sender_number' => '01712345678', 'amount_paid' => 2535, 'terms' => '1',
        ];

        $this->post(route('register.store'), $payload)->assertSessionHasErrors('session');
        $this->assertSame(0, Registration::count());

        $this->post(route('register.store'), ['session' => '2008-09'] + $payload)
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('2008-09', Registration::firstOrFail()->session);
    }

    /** An imported record with an odd session must stay editable. */
    #[Test]
    public function the_admin_form_keeps_a_legacy_session_selectable(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $r = $this->alumnus('Legacy Person', '2008-09');
        // Bypass the mutator to mimic a row imported before normalisation.
        DB::table('registrations')
            ->where('id', $r->id)->update(['session' => '1948-49']);

        $this->actingAs($admin)
            ->get(route('admin.registrations.edit', $r))
            ->assertOk()
            ->assertSee('1948-49 (as recorded)')
            ->assertSee('<select id="s-session" name="session"', false);
    }
}
