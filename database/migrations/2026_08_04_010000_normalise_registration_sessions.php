<?php

use App\Models\Registration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The directory groups alumni by batch. Sessions typed before normalisation
 * existed may read 2008-2009 or ২০০৮-০৯, which would split one batch across
 * several headings, so they are rewritten to the canonical form here.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('registrations')->select('id', 'session')->orderBy('id')
            ->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $canonical = Registration::normaliseSession($row->session);

                    if ($canonical !== null && $canonical !== $row->session) {
                        DB::table('registrations')->where('id', $row->id)
                            ->update(['session' => $canonical]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Normalisation is not reversible — the original spellings are gone.
    }
};
