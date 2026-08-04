<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LegacyImportTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_imports_the_legacy_entries_as_pending_and_keeps_them_out_of_the_directory(): void
    {
        $this->artisan('registrations:import-legacy')->assertSuccessful();

        $this->assertSame(2, Registration::count());
        $this->assertSame(2, Registration::pending()->count());
        $this->assertSame(0, Registration::verified()->count());

        $legacy = Registration::where('reference', 'LEGACY-11')->sole();
        $this->assertSame('mdrashedulsumon@gmail.com', $legacy->email);
        $this->assertSame(0, $legacy->amount_paid);
        $this->assertStringContainsString('did not capture', $legacy->admin_note);

        // Unverified rows must never surface publicly.
        $this->get(route('directory'))->assertOk()->assertDontSee('mdrashedulsumon@gmail.com');
    }

    #[Test]
    public function it_is_safe_to_run_twice(): void
    {
        $this->artisan('registrations:import-legacy')->assertSuccessful();
        $this->artisan('registrations:import-legacy')->assertSuccessful();

        $this->assertSame(2, Registration::count());
    }
}
