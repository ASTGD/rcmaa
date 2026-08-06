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
        $timeline = config('heritage.timeline');

        $this->assertCount(11, $timeline, 'The association supplied 11 timeline entries.');

        $body = $this->get(route('heritage'))->assertOk()->getContent();

        // Escaped, because several entries carry apostrophes Blade turns into
        // entities — 'Math Nexus Reunion 2026' among them.
        foreach ($timeline as $m) {
            $this->assertStringContainsString(e($m['heading_bn']), $body, "Missing: {$m['year']} {$m['heading_bn']}");
            $this->assertStringContainsString(e($m['body_bn']), $body, "Missing body: {$m['year']}");
        }
    }

    /**
     * The association interleaves each mathematics milestone with the college
     * background around it. Rendering must not quietly sort that back into date
     * order — 1878 deliberately precedes 1873.
     */
    #[Test]
    public function it_keeps_the_order_the_association_gave(): void
    {
        $body = $this->get(route('heritage'))->assertOk()->getContent();

        $positions = array_map(
            fn ($m) => strpos($body, e($m['heading_bn'])),
            config('heritage.timeline')
        );

        $sorted = $positions;
        sort($sorted);

        $this->assertSame($sorted, $positions, 'The timeline was reordered.');
        $this->assertLessThan(
            strpos($body, 'ব্যাকগ্রাউন্ড: ইন্টারমিডিয়েট ও ডিগ্রি কোর্স চালু'),
            strpos($body, 'গণিত বিভাগের ঐতিহাসিক সূচনালগ্ন'),
            '1878 must come before the 1873–1877 background, as supplied.'
        );
    }

    #[Test]
    public function it_spans_the_full_range_of_years(): void
    {
        $this->get(route('heritage'))
            ->assertOk()
            ->assertSee('1873')   // the college's own beginnings, as background
            ->assertSee('1878')   // the department is founded
            ->assertSee('2026')   // RCMAA formed
            ->assertSee('আমাদের ঐতিহ্য', false)
            // The spelling the association asked for, in their own wording.
            ->assertSee('২৮ ফেব্রুয়ারি ২০২৬', false);
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
            'directory', 'contact', 'faqs', 'member.login'] as $route) {
            $this->assertStringContainsString(
                'href="'.route($route).'"', $body, "Nav missing a link to: {$route}"
            );
        }

        // Notice is not one of the nine; it lives in the footer instead.
        $this->assertStringContainsString('href="'.route('notices.index').'"', $body);

        // The spec's ninth item is লগইন. It now serves alumni, who have no
        // password — the committee's own sign-in is one click on from there.
        $this->get(route('member.login'))->assertOk()->assertSee('Send me a link');
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
