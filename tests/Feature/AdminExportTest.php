<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use App\Support\RegistrationPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The CSV is what the committee reconciles money and catering from. Its header
 * and its values drifted apart once — every column after the third was shifted,
 * so "Amount Paid" silently carried somebody else's data. These tests pin the
 * alignment down by value, not by position.
 */
class AdminExportTest extends TestCase
{
    use RefreshDatabase;

    private function registration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'category' => 'alumni',
            'category_fee' => RegistrationPricing::fee('alumni'),
            'guest_fee' => RegistrationPricing::guestFee(),
            'full_name_en' => 'Md. Rofikul Islam',
            'full_name_bn' => 'মোঃ রফিকুল ইসলাম',
            'blood_group' => 'B+',
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
            'guest_count' => '1',
            'guests' => [['name' => 'Shirin Akter', 'relation' => 'Spouse', 'occupation' => 'Teacher']],
            'payment_method' => 'bkash',
            'transaction_id' => 'A1B2C3D4E5',
            'sender_number' => '01712345678',
            'amount_paid' => 5535,
            'amount_due' => 5535,
        ], $overrides));
    }

    private function csv(array $query = []): array
    {
        $this->registration();

        $content = $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.registrations.export', $query))
            ->assertOk()
            ->streamedContent();

        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $rows = array_map('str_getcsv', array_filter(explode("\n", trim($content))));

        return array_combine($rows[0], $rows[1]);
    }

    #[Test]
    public function every_column_holds_the_value_its_heading_promises(): void
    {
        $row = $this->csv();

        $this->assertSame('Md. Rofikul Islam', $row['Name (English)']);
        $this->assertSame('মোঃ রফিকুল ইসলাম', $row['Name (Bangla)']);
        $this->assertSame('Alumnus', $row['Category']);
        $this->assertSame('B+', $row['Blood Group']);
        $this->assertSame('01712345678', $row['Mobile']);
        $this->assertSame('rofikul@example.com', $row['Email']);
        $this->assertSame('XL', $row['T-Shirt']);
        $this->assertSame('A1B2C3D4E5', $row['Transaction ID']);
        $this->assertSame('5535', $row['Amount Paid']);
        $this->assertSame('5535', $row['Amount Due']);
        $this->assertSame('2535', $row['Category Fee']);
        $this->assertSame('3000', $row['Guest Fee (each)']);
        $this->assertSame('1', $row['Guests']);
        $this->assertStringContainsString('Shirin Akter', $row['Guest Details']);
    }

    #[Test]
    public function the_header_and_every_data_row_have_the_same_width(): void
    {
        $this->registration();
        $this->registration(['transaction_id' => 'ZZZZZZZZ', 'email' => 'two@example.com', 'category' => 'current_student']);

        $content = $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.registrations.export'))->assertOk()->streamedContent();
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $rows = array_map('str_getcsv', array_filter(explode("\n", trim($content))));
        $width = count($rows[0]);

        foreach ($rows as $i => $row) {
            $this->assertCount($width, $row, "Row {$i} does not match the header width.");
        }
    }

    #[Test]
    public function the_export_respects_the_category_filter(): void
    {
        $this->registration();
        $this->registration(['transaction_id' => 'ZZZZZZZZ', 'email' => 'st@example.com', 'category' => 'current_student']);

        $content = $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.registrations.export', ['category' => 'current_student']))
            ->assertOk()->streamedContent();

        $this->assertStringContainsString('st@example.com', $content);
        $this->assertStringNotContainsString('rofikul@example.com', $content);
    }

    #[Test]
    public function an_empty_export_still_produces_a_header(): void
    {
        $content = $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get(route('admin.registrations.export'))->assertOk()->streamedContent();

        $this->assertStringContainsString('Reference', $content);
        $this->assertStringContainsString('Amount Paid', $content);
    }
}
