<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Registrants type their transaction ID by hand. The receipt is the only
 * evidence on file when that goes wrong, so it has to survive registration,
 * be visible to the committee, and never become a way to edit the payment.
 */
class PaymentReceiptTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'category' => 'alumni',
            'full_name_en' => 'Md. Rofikul Islam',
            'mobile' => '01712345678',
            'email' => 'rofikul@example.com',
            'present_address' => 'Rajshahi',
            'session' => '2008-09',
            'degree' => 'both',
            'masters_session' => '2012-13',
            'passing_year' => 2012,
            'employment_status' => 'employed',
            'profession' => 'Education',
            'organization' => 'Rajshahi College',
            'tshirt_size' => 'XL',
            'cultural_program' => '1',
            'guest_count' => '0',
            'payment_method' => 'bkash',
            'transaction_id' => 'A1B2C3D4E5',
            'sender_number' => '01712345678',
            'amount_paid' => 2535,
            'terms' => '1',
        ], $overrides);
    }

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'category' => 'alumni', 'category_fee' => 2535, 'guest_fee' => 500,
            'full_name_en' => 'Md. Rofikul Islam', 'mobile' => '01712345678',
            'email' => 'rofikul@example.com', 'present_address' => 'Rajshahi',
            'session' => '2008-09', 'degree' => 'both',
            'masters_session' => '2012-13', 'passing_year' => 2012,
            'employment_status' => 'employed', 'tshirt_size' => 'XL',
            'cultural_program' => true, 'guest_count' => '0', 'guests' => [],
            'payment_method' => 'bkash', 'transaction_id' => 'A1B2C3D4E5',
            'sender_number' => '01712345678', 'amount_paid' => 2535,
            'amount_due' => 2535, 'payment_status' => Registration::STATUS_PENDING,
        ], $overrides));
    }

    private function openPortal(Registration $r): void
    {
        $this->get(URL::temporarySignedRoute('portal.open', now()->addHour(), ['registration' => $r->reference]));
    }

    #[Test]
    public function a_receipt_submitted_with_the_registration_is_stored(): void
    {
        Storage::fake('public');

        $this->post(route('register.store'), $this->payload([
            'payment_receipt' => UploadedFile::fake()->image('bkash.jpg'),
        ]))->assertRedirect()->assertSessionHasNoErrors();

        $r = Registration::firstOrFail();
        $this->assertNotNull($r->payment_receipt_path);
        Storage::disk('public')->assertExists($r->payment_receipt_path);
        $this->assertStringStartsWith('registrations/receipts/', $r->payment_receipt_path);
    }

    /** Nobody who has already deleted the SMS may be locked out of registering. */
    #[Test]
    public function the_receipt_is_optional(): void
    {
        Storage::fake('public');

        $this->post(route('register.store'), $this->payload())
            ->assertRedirect()->assertSessionHasNoErrors();

        $this->assertNull(Registration::firstOrFail()->payment_receipt_path);
    }

    #[Test]
    public function a_pdf_bank_slip_is_accepted(): void
    {
        Storage::fake('public');

        $this->post(route('register.store'), $this->payload([
            'payment_receipt' => UploadedFile::fake()->create('slip.pdf', 200, 'application/pdf'),
        ]))->assertRedirect()->assertSessionHasNoErrors();

        $r = Registration::firstOrFail();
        $this->assertTrue($r->payment_receipt_is_pdf);
    }

    #[Test]
    public function an_executable_masquerading_as_a_receipt_is_refused(): void
    {
        Storage::fake('public');

        $this->post(route('register.store'), $this->payload([
            'payment_receipt' => UploadedFile::fake()->create('payload.php', 10, 'application/x-php'),
        ]))->assertSessionHasErrors('payment_receipt');

        $this->assertSame(0, Registration::count());
    }

    #[Test]
    public function an_oversized_receipt_is_refused(): void
    {
        Storage::fake('public');

        $this->post(route('register.store'), $this->payload([
            'payment_receipt' => UploadedFile::fake()->create('huge.jpg', 5000, 'image/jpeg'),
        ]))->assertSessionHasErrors('payment_receipt');
    }

    #[Test]
    public function the_committee_sees_the_receipt_on_the_record(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $r = $this->registration(['payment_receipt_path' => 'registrations/receipts/proof.jpg']);

        $this->actingAs($admin)
            ->get(route('admin.registrations.show', $r))
            ->assertOk()
            ->assertSee('registrations/receipts/proof.jpg');
    }

    #[Test]
    public function the_committee_is_told_when_no_receipt_was_attached(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $r = $this->registration();

        $this->actingAs($admin)
            ->get(route('admin.registrations.show', $r))
            ->assertOk()
            ->assertSee('None attached');
    }

    #[Test]
    public function a_registrant_can_attach_a_receipt_afterwards(): void
    {
        Storage::fake('public');
        $r = $this->registration();
        $this->openPortal($r);

        $this->post(route('portal.receipt'), [
            'payment_receipt' => UploadedFile::fake()->image('late.jpg'),
        ])->assertRedirect()->assertSessionHasNoErrors();

        $r->refresh();
        $this->assertNotNull($r->payment_receipt_path);
        Storage::disk('public')->assertExists($r->payment_receipt_path);
    }

    #[Test]
    public function replacing_a_receipt_removes_the_previous_file(): void
    {
        Storage::fake('public');
        $r = $this->registration();
        $this->openPortal($r);

        $this->post(route('portal.receipt'), ['payment_receipt' => UploadedFile::fake()->image('first.jpg')]);
        $first = $r->fresh()->payment_receipt_path;

        $this->post(route('portal.receipt'), ['payment_receipt' => UploadedFile::fake()->image('second.jpg')]);
        $second = $r->fresh()->payment_receipt_path;

        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);
    }

    /** The upload route writes one column and no more. */
    #[Test]
    public function uploading_a_receipt_cannot_alter_the_payment(): void
    {
        Storage::fake('public');
        $r = $this->registration();
        $this->openPortal($r);

        $this->post(route('portal.receipt'), [
            'payment_receipt' => UploadedFile::fake()->image('proof.jpg'),
            'amount_paid' => 999999,
            'amount_due' => 0,
            'payment_status' => Registration::STATUS_VERIFIED,
            'transaction_id' => 'HIJACKED',
            'payment_method' => 'bank',
        ])->assertRedirect();

        $r->refresh();
        $this->assertSame(2535, $r->amount_paid);
        $this->assertSame(2535, $r->amount_due);
        $this->assertSame(Registration::STATUS_PENDING, $r->payment_status);
        $this->assertSame('A1B2C3D4E5', $r->transaction_id);
        $this->assertSame('bkash', $r->payment_method);
    }

    #[Test]
    public function the_upload_route_is_closed_without_a_valid_link(): void
    {
        Storage::fake('public');

        $this->post(route('portal.receipt'), ['payment_receipt' => UploadedFile::fake()->image('x.jpg')])
            ->assertRedirect(route('portal.request'));
    }

    #[Test]
    public function the_export_records_whether_a_receipt_is_on_file(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->registration(['payment_receipt_path' => 'registrations/receipts/proof.jpg']);
        $this->registration(['email' => 'b@example.com', 'transaction_id' => 'ZZZZ111111']);

        $csv = $this->actingAs($admin)->get(route('admin.registrations.export'))->streamedContent();
        $rows = array_map('str_getcsv', array_filter(explode("\n", trim($csv))));

        $column = array_search('Receipt', $rows[0], true);
        $this->assertNotFalse($column, 'The export needs a Receipt column.');
        $this->assertSame('Attached', $rows[1][$column]);
        $this->assertSame('', $rows[2][$column]);
    }
}
