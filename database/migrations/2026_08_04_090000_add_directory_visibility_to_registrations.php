<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Privacy Policy promises that a registrant can have their entry taken
     * out of the public directory on request. Without this flag the only way to
     * honour that was to delete the registration or un-verify it — either of
     * which also revokes their confirmed seat.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->boolean('listed_in_directory')->default(true)->after('payment_status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('listed_in_directory');
        });
    }
};
