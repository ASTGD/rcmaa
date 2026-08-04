<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Someone who did both degrees here has two sessions, and "Session" alone did
 * not say which they had given. `session` stays the batch they were admitted in
 * — the one the directory groups by — and this holds the Masters session
 * alongside it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('masters_session', 32)->nullable()->after('session');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('masters_session');
        });
    }
};
