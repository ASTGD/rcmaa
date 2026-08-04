<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use App\Support\RegistrationPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The operational gaps: correcting a registration, honouring a directory
 * opt-out, and managing committee accounts. Each of these existed as a promise
 * on the public site before it existed as a capability in the admin.
 */
class AdminOperationsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'category' => 'alumni',
            'category_fee' => 2535,
            'guest_fee' => 3000,
            'full_name_en' => 'Md. Rofikul Islam',
            'mobile' => '01712345678',
            'email' => 'rofikul@example.com',
            'present_address' => 'Rajshahi',
            'session' => '2008-09',
            'degree' => 'both',
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

    private function details(Registration $r, array $overrides = []): array
    {
        return array_merge([
            'category' => $r->category,
            'full_name_en' => $r->full_name_en,
            'mobile' => $r->mobile,
            'email' => $r->email,
            'present_address' => $r->present_address,
            'session' => $r->session,
            'degree' => $r->degree,
            'passing_year' => $r->passing_year,
            'employment_status' => $r->employment_status,
            'tshirt_size' => $r->tshirt_size,
            'cultural_program' => $r->cultural_program ? '1' : '0',
            'payment_method' => $r->payment_method,
            'transaction_id' => $r->transaction_id,
            'sender_number' => $r->sender_number,
            'amount_paid' => $r->amount_paid,
            'listed_in_directory' => '1',
        ], $overrides);
    }

    #[Test]
    public function the_helpdesk_can_correct_a_mistyped_detail(): void
    {
        $r = $this->registration();

        $this->actingAs($this->admin())
            ->put(route('admin.registrations.update-details', $r), $this->details($r, [
                'tshirt_size' => 'M',
                'mobile' => '01799999999',
            ]))->assertRedirect(route('admin.registrations.show', $r));

        $r->refresh();
        $this->assertSame('M', $r->tshirt_size);
        $this->assertSame('01799999999', $r->mobile);
        // The correction is recorded, so there is a trail of what changed.
        $this->assertStringContainsString('Corrected by', $r->admin_note);
        $this->assertStringContainsString('tshirt_size', $r->admin_note);
    }

    #[Test]
    public function changing_the_category_re_derives_what_is_owed(): void
    {
        $r = $this->registration();

        $this->actingAs($this->admin())
            ->put(route('admin.registrations.update-details', $r), $this->details($r, [
                'category' => 'current_student',
            ]))->assertRedirect();

        $r->refresh();
        $this->assertSame('current_student', $r->category);
        $this->assertSame(RegistrationPricing::fee('current_student'), $r->category_fee);
        $this->assertSame(RegistrationPricing::fee('current_student'), $r->amount_due);
        // That category cannot bring guests, so no guest rate applies.
        $this->assertSame(0, $r->guest_fee);
    }

    #[Test]
    public function a_correction_cannot_steal_another_registrants_transaction_id(): void
    {
        $this->registration(['transaction_id' => 'TAKEN123', 'email' => 'other@example.com']);
        $r = $this->registration(['transaction_id' => 'MINE456', 'email' => 'mine@example.com']);

        $this->actingAs($this->admin())
            ->put(route('admin.registrations.update-details', $r), $this->details($r, [
                'transaction_id' => 'TAKEN123',
            ]))->assertSessionHasErrors('transaction_id');
    }

    #[Test]
    public function a_registrant_can_be_removed_from_the_directory_without_losing_their_seat(): void
    {
        $r = $this->registration(['full_name_en' => 'Opt Out Person']);

        $this->get(route('directory'))->assertOk()->assertSee('Opt Out Person');

        $this->actingAs($this->admin())
            ->put(route('admin.registrations.update-details', $r),
                $this->details($r, ['listed_in_directory' => '0']))
            ->assertRedirect();

        $r->refresh();
        $this->assertFalse($r->listed_in_directory);
        // Still verified — their seat is intact.
        $this->assertSame(Registration::STATUS_VERIFIED, $r->payment_status);

        $this->get(route('directory'))->assertOk()->assertDontSee('Opt Out Person');
    }

    // --- Committee accounts -------------------------------------------------

    #[Test]
    public function an_admin_can_add_another_committee_account(): void
    {
        $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'name' => 'Registration Sub-committee',
            'email' => 'registration@rcmaa.bd',
            'password' => 'a-long-enough-secret',
            'password_confirmation' => 'a-long-enough-secret',
            'is_admin' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $new = User::where('email', 'registration@rcmaa.bd')->sole();
        $this->assertTrue($new->is_admin);
        $this->assertTrue(Hash::check('a-long-enough-secret', $new->password));
    }

    #[Test]
    public function short_passwords_are_rejected(): void
    {
        $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'name' => 'Weak', 'email' => 'weak@rcmaa.bd',
            'password' => 'short', 'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    #[Test]
    public function the_last_administrator_cannot_be_demoted_or_deleted(): void
    {
        $only = $this->admin();
        $other = User::factory()->create(['is_admin' => false]);

        // Demoting yourself as the only admin.
        $this->actingAs($only)->put(route('admin.users.update', $only), [
            'name' => $only->name, 'email' => $only->email,
        ])->assertSessionHasErrors('is_admin');
        $this->assertTrue($only->fresh()->is_admin);

        // Deleting yourself.
        $this->actingAs($only)->delete(route('admin.users.destroy', $only))
            ->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $only->id]);

        // A non-admin can still be removed.
        $this->actingAs($only)->delete(route('admin.users.destroy', $other));
        $this->assertDatabaseMissing('users', ['id' => $other->id]);
    }

    #[Test]
    public function an_admin_can_change_their_own_password_but_must_prove_the_current_one(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'the-old-password']);

        $this->actingAs($admin)->put(route('admin.account.update'), [
            'name' => $admin->name, 'email' => $admin->email,
            'current_password' => 'wrong-password',
            'password' => 'a-brand-new-secret', 'password_confirmation' => 'a-brand-new-secret',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($admin)->put(route('admin.account.update'), [
            'name' => $admin->name, 'email' => $admin->email,
            'current_password' => 'the-old-password',
            'password' => 'a-brand-new-secret', 'password_confirmation' => 'a-brand-new-secret',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('a-brand-new-secret', $admin->fresh()->password));
    }

    #[Test]
    public function account_management_is_closed_to_non_admins(): void
    {
        $plain = User::factory()->create(['is_admin' => false]);

        foreach (['admin.users.index', 'admin.users.create', 'admin.account'] as $route) {
            $this->actingAs($plain)->get(route($route))->assertForbidden();
        }
    }

    /**
     * Verifying a payment used to write listed_in_directory from a checkbox the
     * verification form does not have, so boolean() was always false and every
     * verification quietly unlisted the registrant. Verifying is the routine
     * action for everybody, so the public directory drained as the committee
     * worked through the queue.
     */
    #[Test]
    public function verifying_a_payment_leaves_the_directory_listing_alone(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $r = $this->registration(['listed_in_directory' => true]);

        $this->actingAs($admin)->patch(route('admin.registrations.update', $r), [
            'payment_status' => 'verified',
            'admin_note' => 'Matched against the bKash statement.',
        ])->assertRedirect();

        $r->refresh();
        $this->assertSame(Registration::STATUS_VERIFIED, $r->payment_status);
        $this->assertTrue($r->listed_in_directory, 'Verification must not unlist anybody.');

        $this->get(route('directory'))->assertOk()->assertSee($r->full_name_en);
    }

    /** And an opt-out must survive verification too. */
    #[Test]
    public function verifying_a_payment_does_not_re_list_someone_who_opted_out(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $r = $this->registration(['listed_in_directory' => false, 'full_name_en' => 'Wants Privacy']);

        $this->actingAs($admin)->patch(route('admin.registrations.update', $r), [
            'payment_status' => 'verified',
        ])->assertRedirect();

        $this->assertFalse($r->fresh()->listed_in_directory);
        $this->get(route('directory'))->assertOk()->assertDontSee('Wants Privacy');
    }
}
