<?php

namespace Tests\Feature;

use App\Models\CommitteeMember;
use App\Models\User;
use Database\Seeders\CommitteeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommitteePrivacyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The committee roster carries every member's personal mobile number. They
     * are stored so the committee can work from the admin, but publishing them
     * would expose 48 people's private numbers to the open web.
     */
    #[Test]
    public function committee_mobile_numbers_never_appear_on_a_public_page(): void
    {
        $this->seed(CommitteeSeeder::class);

        // The association designates one committee member's mobile as its public
        // contact number, so that one is published by intent. Every other number
        // on the roster is private.
        $published = collect([
            config('rcmaa.contact.phone'),
            config('rcmaa.contact.hotline'),
            config('rcmaa.contact.helpdesk'),
        ])->map(fn ($n) => preg_replace('/\D/', '', (string) $n))->filter();

        $phones = CommitteeMember::whereNotNull('phone')->pluck('phone')
            ->reject(fn ($p) => $published->contains(
                fn ($n) => str_ends_with($n, preg_replace('/\D/', '', $p))
            ));

        $this->assertGreaterThan(40, $phones->count(), 'The roster should be seeded.');

        $routes = [route('home'), route('about'), route('our-goal')];

        // The directory is members-only now, so it is swept while signed in —
        // a committee member's private number must not leak there either.
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $routes[] = route('directory');

        foreach (array_keys(CommitteeMember::COMMITTEES) as $group) {
            $routes[] = route('committee', ['group' => $group]);
        }

        foreach ($routes as $url) {
            $body = $this->get($url)->assertOk()->getContent();

            foreach ($phones as $phone) {
                $this->assertStringNotContainsString($phone, $body, "{$phone} leaked on {$url}");
                // Also check the digits-only form, in case of a tel: link.
                $this->assertStringNotContainsString(
                    preg_replace('/\D/', '', $phone), $body, "{$phone} leaked (unformatted) on {$url}"
                );
            }
        }
    }

    #[Test]
    public function the_committee_page_still_shows_names_sessions_and_roles(): void
    {
        $this->seed(CommitteeSeeder::class);

        $this->get(route('committee', ['group' => 'reunion_convening']))
            ->assertOk()
            ->assertSee('Md. Rofikul Islam')
            ->assertSee('Convenor')
            ->assertSee('1995-96');
    }

    #[Test]
    public function an_admin_can_see_the_mobile_numbers(): void
    {
        $this->seed(CommitteeSeeder::class);
        $member = CommitteeMember::whereNotNull('phone')->first();

        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.content.edit', ['committee', $member->id]))
            ->assertOk()
            ->assertSee($member->phone);
    }
}
