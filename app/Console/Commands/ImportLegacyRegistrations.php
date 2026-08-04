<?php

namespace App\Console\Commands;

use App\Models\Registration;
use App\Support\RegistrationPricing;
use Illuminate\Console\Command;

/**
 * Carries the old WordPress site's MetForm submissions into `registrations`.
 *
 * The legacy form captured far fewer fields than this one — it recorded no name,
 * session, address or transaction ID — so every imported row lands as `pending`
 * with the gaps stated plainly in its admin note. Nothing imported here can
 * reach the public directory until a human verifies it, which is the same gate
 * every other registration passes through.
 */
class ImportLegacyRegistrations extends Command
{
    protected $signature = 'registrations:import-legacy {--force : Re-import even if the reference already exists}';

    protected $description = 'Import the old rcmaa.bd MetForm entries into the registrations table';

    /**
     * Transcribed from WP Admin → MetForm → Entries on 3 August 2026.
     * `_raw` holds every value the legacy form actually stored, so nothing is
     * lost even where this application has no matching column.
     */
    private const ENTRIES = [
        [
            'entry' => 11,
            'submitted_at' => '2026-08-02 13:59:53',
            'submitted_by' => 'Visitor',
            'email' => 'mdrashedulsumon@gmail.com',
            'mobile' => '01748963995',
            'blood_group' => 'B+',
            'degree' => 'bsc',
            'passing_year' => 2005,
            'employment_status' => 'self_employed',
            'profession' => 'business',
            'organization' => 'online',
            'tshirt_size' => 'XL',
            'cultural_program' => true,
            'guest_count' => '1',
            'memories' => 'spending Enjoyable time that days',
            'payment_method' => 'bkash',
            '_raw' => ['occupation' => 'business', 'organization' => 'online'],
        ],
        [
            'entry' => 12,
            'submitted_at' => '2026-08-02 18:29:32',
            'submitted_by' => 'admircmaad',
            'email' => 'mamtazur@hotmail.com',
            'mobile' => '01716303452',
            'blood_group' => 'B+',
            'degree' => 'both',
            'passing_year' => 2005,
            'employment_status' => 'employed',
            'profession' => 'business',
            'organization' => 'nwdc',
            'tshirt_size' => 'L',
            'cultural_program' => true,
            'guest_count' => '1',
            'memories' => 'wonderful day',
            'payment_method' => 'bkash',
            '_raw' => ['occupation' => 'business', 'organization' => 'nwdc'],
        ],
    ];

    public function handle(): int
    {
        $imported = 0;
        $skipped = 0;

        foreach (self::ENTRIES as $e) {
            $reference = "LEGACY-{$e['entry']}";

            if (Registration::where('reference', $reference)->exists()) {
                if (! $this->option('force')) {
                    $this->line("  skipped {$reference} (already imported)");
                    $skipped++;

                    continue;
                }
                Registration::where('reference', $reference)->delete();
            }

            $guests = (int) $e['guest_count'];
            // The legacy form had no category; these are all past graduates.
            $category = 'alumni';
            $due = RegistrationPricing::total($category, $guests);

            $note = collect([
                "Imported from the old rcmaa.bd site — MetForm entry #{$e['entry']}, submitted {$e['submitted_at']} by {$e['submitted_by']}.",
                'The legacy form did not capture: full name, session, present address, transaction ID, or amount paid.',
                'Amount paid is recorded as 0 because no payment record exists — confirm with the registrant before verifying.',
                $guests > 0 ? "Declared {$guests} guest(s); no guest names were captured." : null,
                'Legacy values: '.json_encode($e['_raw'], JSON_UNESCAPED_UNICODE),
            ])->filter()->implode("\n\n");

            Registration::create([
                'reference' => $reference,
                'category' => $category,
                'category_fee' => RegistrationPricing::fee($category),
                'guest_fee' => RegistrationPricing::guestFee(),
                // Deliberately not a guessed name — the legacy form never asked for one.
                'full_name_en' => "[Legacy entry #{$e['entry']} — name not captured]",
                'mobile' => $e['mobile'],
                'email' => $e['email'],
                'blood_group' => $e['blood_group'],
                'present_address' => 'Not captured by the legacy form',
                'session' => 'Unknown',
                'degree' => $e['degree'],
                'passing_year' => $e['passing_year'],
                'employment_status' => $e['employment_status'],
                'profession' => $e['profession'],
                'organization' => $e['organization'],
                'tshirt_size' => $e['tshirt_size'],
                'cultural_program' => $e['cultural_program'],
                'guest_count' => $e['guest_count'],
                'guests' => array_fill(0, $guests, ['name' => 'Not captured', 'relation' => null, 'occupation' => null]),
                'memories' => $e['memories'],
                'payment_method' => $e['payment_method'],
                'transaction_id' => $reference,
                'sender_number' => $e['mobile'],
                'amount_paid' => 0,
                'amount_due' => $due,
                'payment_status' => Registration::STATUS_PENDING,
                'admin_note' => $note,
                'created_at' => $e['submitted_at'],
                'updated_at' => $e['submitted_at'],
            ]);

            $this->info("  imported {$reference}  {$e['email']}  ({$e['tshirt_size']}, {$guests} guest)");
            $imported++;
        }

        $this->newLine();
        $this->info("Imported {$imported}, skipped {$skipped}.");
        $this->comment('All rows are PENDING and carry an admin note listing the missing fields.');
        $this->comment('None will appear in the public directory until a human verifies them.');

        return self::SUCCESS;
    }
}
