<?php

namespace Tests\Feature;

use App\Models\CommitteeMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HeritageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_every_milestone_from_the_association_document(): void
    {
        $milestones = config('heritage');

        $this->assertCount(28, $milestones, 'The association supplied 28 milestones.');

        $body = $this->get(route('heritage'))->assertOk()->getContent();

        foreach ($milestones as $m) {
            $this->assertStringContainsString($m['heading_bn'], $body, "Missing: {$m['year']} {$m['heading_bn']}");
        }
    }

    #[Test]
    public function it_spans_the_full_range_of_years(): void
    {
        $this->get(route('heritage'))
            ->assertOk()
            ->assertSee('1873')   // raised to intermediate college
            ->assertSee('1971')   // the Liberation War
            ->assertSee('2026')   // RCMAA formed
            ->assertSee('আমাদের ঐতিহ্য', false);
    }

    /**
     * The specification names nine primary nav items and four committees. The
     * site runs an English UI with Bangla alongside (agreed at the outset), so
     * the top level is English and the Bangla sits on the sub-items and in the
     * mobile drawer.
     */
    #[Test]
    public function the_navigation_matches_the_specification(): void
    {
        $body = $this->get(route('home'))->assertOk()->getContent();

        // The nine primary destinations the specification calls for.
        foreach (['home', 'about', 'committee', 'events.index', 'gallery',
            'directory', 'contact', 'faqs', 'portal.request'] as $route) {
            $this->assertStringContainsString(
                'href="'.route($route).'"', $body, "Nav missing a link to: {$route}"
            );
        }

        // Notice is not one of the nine; it lives in the footer instead.
        $this->assertStringContainsString('href="'.route('notices.index').'"', $body);

        // The spec's ninth item is লগইন. It now serves alumni, who have no
        // password — the committee's own sign-in is one click on from there.
        $this->get(route('portal.request'))->assertOk()->assertSee('Send me a link');
        $this->get(route('login'))->assertOk();

        // The Bangla the association specified, carried on the sub-items.
        foreach (['আমাদের ঐতিহ্য', 'ইতিহাস', 'আমাদের পথচলা', 'লক্ষ্য', 'উদ্দেশ্য',
            'উপদেষ্টা কমিটি', 'আহ্বায়ক কমিটি', 'রিইউনিয়ন উপকমিটি', 'ব্যাচ প্রতিনিধি'] as $label) {
            $this->assertStringContainsString($label, $body, "Bangla nav label missing: {$label}");
        }

        $this->assertSame(
            ['advisory', 'reunion_convening', 'reunion_sub', 'batch_rep'],
            array_keys(CommitteeMember::COMMITTEES)
        );
    }

    #[Test]
    public function the_our_goal_page_carries_all_six_aims_and_seven_objectives(): void
    {
        $this->get(route('our-goal'))
            ->assertOk()
            ->assertSee('id="aims"', false)
            ->assertSee('id="objectives"', false)
            // One distinctive line from each list.
            ->assertSee('চিকিৎসা বা জরুরি প্রয়োজনে', false)
            ->assertSee('সেশনভিত্তিক তথ্যভান্ডার', false);
    }
}
