<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registrants type their transaction ID by hand, so a single mistyped
 * character makes a payment unverifiable. Keeping the confirmation screenshot
 * alongside it gives the committee something to check against.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('payment_receipt_path')->nullable()->after('sender_number');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('payment_receipt_path');
        });
    }
};
