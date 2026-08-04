<?php

namespace Tests\Feature;

use App\Models\CommitteeMember;
use App\Models\Event;
use App\Models\Notice;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public static function routes(): array
    {
        return [
            'home' => ['home'],
            'about' => ['about'],
            'our goal' => ['our-goal'],
            'faculty' => ['teachers'],
            'committee' => ['committee'],
            'events' => ['events.index'],
            'notices' => ['notices.index'],
            'gallery' => ['gallery'],
            'directory' => ['directory'],
            'faqs' => ['faqs'],
            'contact' => ['contact'],
            'how to apply' => ['how-to-apply'],
            'features' => ['features'],
            'help center' => ['help-center'],
            'privacy' => ['privacy'],
            'terms' => ['terms'],
            'login' => ['login'],
            'register' => ['register.create'],
            'registration status' => ['registration.status'],
        ];
    }

    #[Test]
    #[DataProvider('routes')]
    public function it_renders_public_pages_with_no_content(string $route): void
    {
        $this->get(route($route))->assertOk();
    }

    #[Test]
    #[DataProvider('routes')]
    public function it_renders_public_pages_with_seeded_content(string $route): void
    {
        $this->seed(ContentSeeder::class);

        $this->get(route($route))->assertOk();
    }

    #[Test]
    public function it_renders_every_committee_group(): void
    {
        $this->seed(ContentSeeder::class);

        foreach (array_keys(CommitteeMember::COMMITTEES) as $group) {
            $this->get(route('committee', ['group' => $group]))->assertOk();
        }
    }

    #[Test]
    public function it_renders_event_and_notice_detail_pages(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get(route('events.show', Event::firstOrFail()))->assertOk();
        $this->get(route('notices.show', Notice::firstOrFail()))->assertOk();
    }

    #[Test]
    public function it_hides_unpublished_events_and_notices(): void
    {
        $this->seed(ContentSeeder::class);

        $event = Event::firstOrFail();
        $event->update(['is_published' => false]);
        $this->get(route('events.show', $event))->assertNotFound();

        $notice = Notice::firstOrFail();
        $notice->update(['is_published' => false]);
        $this->get(route('notices.show', $notice))->assertNotFound();
    }

    #[Test]
    public function it_accepts_a_contact_message(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Shirin Akter',
            'email' => 'shirin@example.com',
            'phone' => '01712345678',
            'subject' => 'Sponsorship enquiry',
            'message' => 'We would like to sponsor the Grand Reunion 2026. Who should we speak to?',
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', ['email' => 'shirin@example.com']);
    }

    #[Test]
    public function it_rejects_a_contact_message_that_fills_the_honeypot(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'Spam Bot',
            'email' => 'bot@example.com',
            'subject' => 'Cheap watches',
            'message' => str_repeat('buy now ', 5),
            'website' => 'https://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }
}
