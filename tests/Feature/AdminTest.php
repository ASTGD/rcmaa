<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\GalleryItem;
use App\Models\Notice;
use App\Models\Registration;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /** The directory is members-only; these checks view it as the committee. */
    private function viewingDirectory(): self
    {
        return $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'full_name_en' => 'Md. Rofikul Islam',
            'mobile' => '01712345678',
            'email' => 'rofikul@example.com',
            'present_address' => 'Rajshahi', 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
            'session' => '2008-09',
            'degree' => 'both',
            'masters_session' => '2012-13',
            'passing_year' => 2012,
            'employment_status' => 'employed', 'work_location' => 'Rajshahi',
            'profession' => 'Education',
            'organization' => 'Rajshahi College',
            'tshirt_size' => 'XL',
            'cultural_program' => true,
            'guest_count' => '0',
            'guests' => [],
            'payment_method' => 'bkash',
            'transaction_id' => 'A1B2C3D4E5',
            'sender_number' => '01712345678',
            'amount_paid' => 2000,
            'amount_due' => 2000,
        ], $overrides));
    }

    #[Test]
    public function it_redirects_guests_to_the_login_page(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    #[Test]
    public function it_forbids_signed_in_non_admins(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    #[Test]
    public function it_signs_an_admin_in_and_sends_them_to_the_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'secret-password']);

        $this->post(route('login'), ['email' => $admin->email, 'password' => 'secret-password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    #[Test]
    public function it_rejects_bad_credentials(): void
    {
        User::factory()->create(['email' => 'admin@rcmaa.bd', 'password' => 'secret-password']);

        $this->post(route('login'), ['email' => 'admin@rcmaa.bd', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function it_renders_the_dashboard_and_registration_list(): void
    {
        $this->registration();

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Md. Rofikul Islam');

        $this->actingAs($this->admin())
            ->get(route('admin.registrations.index'))
            ->assertOk()
            ->assertSee('RC26-');
    }

    #[Test]
    public function it_filters_registrations_by_status_and_search(): void
    {
        $this->registration();
        $this->registration([
            'full_name_en' => 'Mst. Mafruha Mustari',
            'email' => 'mafruha@example.com',
            'transaction_id' => 'ZZZZZZZZ',
            'payment_status' => Registration::STATUS_VERIFIED,
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.registrations.index', ['status' => 'verified']))
            ->assertOk()
            ->assertSee('Mafruha')
            ->assertDontSee('Rofikul');

        $this->actingAs($admin)
            ->get(route('admin.registrations.index', ['q' => 'rofikul@example.com']))
            ->assertOk()
            ->assertSee('Rofikul')
            ->assertDontSee('Mafruha');
    }

    #[Test]
    public function it_verifies_a_registration_and_records_who_did_it(): void
    {
        $registration = $this->registration();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.registrations.update', $registration), [
                'payment_status' => 'verified',
                'admin_note' => 'Matched against the bKash statement.',
            ])->assertRedirect();

        $registration->refresh();

        $this->assertSame(Registration::STATUS_VERIFIED, $registration->payment_status);
        $this->assertSame($admin->id, $registration->verified_by);
        $this->assertNotNull($registration->verified_at);
    }

    #[Test]
    public function it_clears_verification_when_a_registration_is_moved_back_to_pending(): void
    {
        $registration = $this->registration([
            'payment_status' => Registration::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);

        $this->actingAs($this->admin())
            ->patch(route('admin.registrations.update', $registration), ['payment_status' => 'pending']);

        $registration->refresh();

        $this->assertNull($registration->verified_at);
        $this->assertNull($registration->verified_by);
    }

    #[Test]
    public function it_shows_verified_registrations_in_the_public_directory_only(): void
    {
        $this->registration(['full_name_en' => 'Pending Person']);
        $this->registration([
            'full_name_en' => 'Verified Person',
            // One email, one member — it is what they sign in with now.
            'email' => 'verified.person@example.com',
            'transaction_id' => 'ZZZZZZZZ',
            'payment_status' => Registration::STATUS_VERIFIED,
        ]);

        $this->viewingDirectory()->get(route('directory'))
            ->assertOk()
            ->assertSee('Verified Person')
            ->assertDontSee('Pending Person');
    }

    /**
     * What the directory publishes, and what it holds back.
     *
     * Mobile and — since 9 August 2026, at the association's instruction —
     * email are both shown so alumni can reach each other. The home address
     * and blood group are not, and must not start appearing by accident.
     *
     * Note for whoever reads this next: the Privacy Policy still enumerates
     * the published fields as "name, session, passing year, profession,
     * photograph and mobile number". Email is now shown as well, so the two
     * disagree. The association was told and chose to publish; the wording
     * needs catching up, and this test is the reminder.
     */
    #[Test]
    public function the_directory_publishes_contact_details_but_not_the_address(): void
    {
        $this->registration([
            'payment_status' => Registration::STATUS_VERIFIED,
            'mobile' => '01755500011',
            'email' => 'reachable@example.com',
            'present_address' => 'A private street address', 'present_district' => 'Rajshahi', 'present_upazila' => 'Paba',
            'blood_group' => 'AB-',
        ]);

        $this->viewingDirectory()->get(route('directory'))
            ->assertOk()
            ->assertSee('01755500011')
            ->assertSee('reachable@example.com')
            ->assertDontSee('A private street address')
            ->assertDontSee('AB-');
    }

    #[Test]
    public function the_privacy_policy_states_which_contact_details_are_published(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('It is not public: only registered members who have signed in')
            ->assertSee('mobile number and email address are listed')
            // The directory publishes the email, so the policy must say so —
            // this is the assertion that keeps the two from drifting apart.
            ->assertSee('your email address')
            ->assertSee('also what you sign in with')
            ->assertSee('are not published');
    }

    #[Test]
    public function the_registration_form_warns_before_the_number_is_given(): void
    {
        $this->get(route('register.create'))
            ->assertOk()
            ->assertSee('Your mobile number and email address will be published');
    }

    #[Test]
    public function it_exports_registrations_as_csv(): void
    {
        $this->registration();

        $response = $this->actingAs($this->admin())->get(route('admin.registrations.export'));

        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Md. Rofikul Islam', $response->streamedContent());
    }

    #[Test]
    public function it_deletes_a_registration_and_its_photo(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('p.jpg')->store('registrations/photos', 'public');
        $registration = $this->registration(['photo_path' => $path]);

        $this->actingAs($this->admin())
            ->delete(route('admin.registrations.destroy', $registration))
            ->assertRedirect(route('admin.registrations.index'));

        $this->assertDatabaseCount('registrations', 0);
        Storage::disk('public')->assertMissing($path);
    }

    #[Test]
    public function it_lists_and_manages_contact_messages(): void
    {
        $message = ContactMessage::create([
            'name' => 'Shirin Akter', 'email' => 'shirin@example.com',
            'subject' => 'Sponsorship', 'message' => 'We would like to sponsor the reunion.',
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.messages.index'))->assertOk()->assertSee('Sponsorship');

        $this->actingAs($admin)->patch(route('admin.messages.update', $message), ['is_read' => 1]);
        $this->assertTrue($message->fresh()->is_read);

        $this->actingAs($admin)->delete(route('admin.messages.destroy', $message));
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public static function contentTypes(): array
    {
        return [
            'committee' => ['committee'],
            'teachers' => ['teachers'],
            'events' => ['events'],
            'notices' => ['notices'],
            'gallery' => ['gallery'],
            'faqs' => ['faqs'],
            'sponsors' => ['sponsors'],
        ];
    }

    #[Test]
    #[DataProvider('contentTypes')]
    public function it_renders_every_cms_list_and_form(string $type): void
    {
        $this->seed(ContentSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.content.index', $type))->assertOk();
        $this->actingAs($admin)->get(route('admin.content.create', $type))->assertOk();
    }

    #[Test]
    public function it_rejects_an_unknown_content_type(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.content.index', 'nonsense'))
            ->assertNotFound();
    }

    #[Test]
    public function it_creates_edits_and_deletes_a_notice_through_the_cms(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.content.store', 'notices'), [
            'title' => 'Committee meeting on Friday',
            'published_on' => '2026-08-10',
            'excerpt' => 'All members are invited.',
            'body' => 'The meeting begins at 4pm in the department.',
            'is_published' => '1',
        ])->assertRedirect(route('admin.content.index', 'notices'));

        $notice = Notice::sole();
        $this->assertSame('committee-meeting-on-friday', $notice->slug);
        $this->assertTrue($notice->is_published);

        $this->actingAs($admin)->get(route('admin.content.edit', ['notices', $notice->id]))->assertOk();

        $this->actingAs($admin)->put(route('admin.content.update', ['notices', $notice->id]), [
            'title' => 'Committee meeting moved to Saturday',
            'published_on' => '2026-08-10',
            'body' => 'Now 4pm on Saturday.',
        ])->assertRedirect();

        $notice->refresh();
        $this->assertSame('Committee meeting moved to Saturday', $notice->title);
        $this->assertFalse($notice->is_published, 'An unchecked publish toggle should unpublish the notice.');

        $this->actingAs($admin)->delete(route('admin.content.destroy', ['notices', $notice->id]));
        $this->assertDatabaseCount('notices', 0);
    }

    #[Test]
    public function it_uploads_and_replaces_a_gallery_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.content.store', 'gallery'), [
            'title' => 'Reunion 2026',
            'category' => 'events',
            'upload' => UploadedFile::fake()->image('one.jpg'),
            'is_published' => '1',
        ])->assertRedirect();

        $item = GalleryItem::sole();
        $original = $item->image_path;
        Storage::disk('public')->assertExists($original);

        $this->actingAs($admin)->put(route('admin.content.update', ['gallery', $item->id]), [
            'title' => 'Reunion 2026',
            'category' => 'events',
            'upload' => UploadedFile::fake()->image('two.jpg'),
        ])->assertRedirect();

        $item->refresh();
        $this->assertNotSame($original, $item->image_path);
        Storage::disk('public')->assertMissing($original);
        Storage::disk('public')->assertExists($item->image_path);
    }

    #[Test]
    public function it_keeps_the_existing_image_when_no_replacement_is_uploaded(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.content.store', 'gallery'), [
            'title' => 'Reunion 2026',
            'category' => 'events',
            'upload' => UploadedFile::fake()->image('one.jpg'),
        ]);

        $item = GalleryItem::sole();
        $original = $item->image_path;

        $this->actingAs($admin)->put(route('admin.content.update', ['gallery', $item->id]), [
            'title' => 'Reunion 2026 — renamed',
            'category' => 'events',
        ])->assertRedirect();

        $item->refresh();
        $this->assertSame($original, $item->image_path);
        Storage::disk('public')->assertExists($original);
    }

    #[Test]
    public function guests_cannot_access_database_backup(): void
    {
        $this->get(route('admin.database.index'))->assertRedirect(route('login'));
        $this->post(route('admin.database.backup'))->assertRedirect(route('login'));
    }

    #[Test]
    public function admins_can_access_database_backup_page(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->get(route('admin.database.index'))
            ->assertOk()
            ->assertSee('Database Backup');
    }

    #[Test]
    public function admins_can_download_database_backup(): void
    {
        $admin = $this->admin();
        $response = $this->actingAs($admin)->post(route('admin.database.backup'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/sql');
        $this->assertStringStartsWith(
            'attachment; filename=backup-' . now()->format('Y-m-d'),
            $response->headers->get('Content-Disposition')
        );
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('-- RCMAA Database Backup', $content);
        
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            $this->assertStringContainsString('PRAGMA foreign_keys = OFF;', $content);
            $this->assertStringContainsString('PRAGMA foreign_keys = ON;', $content);
        } else {
            $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS=0;', $content);
            $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS=1;', $content);
        }
    }
}
