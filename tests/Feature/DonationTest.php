<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Donations are taken outside the registration flow: somebody transfers to the
 * association's bank account and then tells us about it here, and a committee
 * member verifies it against the statement. Money is involved, so the guards
 * matter more than the happy path.
 */
class DonationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'donor_name' => 'Anwar Hossain',
            'phone_number' => '01712345678',
            'amount' => 5000,
            'transaction_id' => 'DONTRX0001',
        ], $overrides);
    }

    #[Test]
    public function a_donation_is_recorded_unverified(): void
    {
        $this->post(route('donation.store'), $this->payload())
            ->assertRedirect()
            ->assertSessionHas('donation_status');

        $donation = Donation::sole();

        $this->assertSame('Anwar Hossain', $donation->donor_name);
        $this->assertSame('5000.00', $donation->amount);
        // Nothing arrives pre-verified — a committee member checks the statement.
        $this->assertFalse($donation->is_verified);
    }

    #[Test]
    public function a_receipt_can_be_attached(): void
    {
        Storage::fake('public');

        $this->post(route('donation.store'), $this->payload([
            'receipt' => UploadedFile::fake()->image('slip.jpg'),
        ]))->assertSessionHasNoErrors();

        $path = Donation::sole()->receipt_path;

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    #[Test]
    public function the_essentials_are_required(): void
    {
        $this->post(route('donation.store'), [])
            ->assertSessionHasErrors(['donor_name', 'phone_number', 'amount', 'transaction_id']);

        $this->assertSame(0, Donation::count());
    }

    #[Test]
    public function a_token_amount_is_refused(): void
    {
        $this->post(route('donation.store'), $this->payload(['amount' => 1]))
            ->assertSessionHasErrors('amount');

        $this->assertSame(0, Donation::count());
    }

    /** The register is not public: it carries donors' names, numbers and amounts. */
    #[Test]
    public function the_donation_register_is_admin_only(): void
    {
        Donation::create($this->payload() + ['is_verified' => false]);

        $this->get(route('admin.donations.index'))->assertRedirect(route('login'));

        $member = User::factory()->create(['is_admin' => false]);
        $this->actingAs($member)->get(route('admin.donations.index'))->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.donations.index'))
            ->assertOk()
            ->assertSee('Anwar Hossain');
    }

    #[Test]
    public function an_admin_verifies_a_donation(): void
    {
        $donation = Donation::create($this->payload() + ['is_verified' => false]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.donations.update', $donation), ['is_verified' => '1'])
            ->assertRedirect();

        $this->assertTrue($donation->fresh()->is_verified);
    }

    /** Deleting the record must not orphan the receipt file. */
    #[Test]
    public function deleting_a_donation_removes_its_receipt(): void
    {
        Storage::fake('public');
        $this->post(route('donation.store'), $this->payload([
            'receipt' => UploadedFile::fake()->image('slip.jpg'),
        ]));

        $donation = Donation::sole();
        $path = $donation->receipt_path;
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->delete(route('admin.donations.destroy', $donation))->assertRedirect();

        $this->assertSame(0, Donation::count());
        Storage::disk('public')->assertMissing($path);
    }
}
