<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Teachers register as staff, not as graduates of the department, so they are
 * not asked for a session, a degree or an employment status. The columns were
 * NOT NULL, which made those steps impossible to skip.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('session', 32)->nullable()->change();
            $table->string('degree', 32)->nullable()->change();
            $table->string('employment_status', 32)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('session', 32)->nullable(false)->change();
            $table->string('degree', 32)->nullable(false)->change();
            $table->string('employment_status', 32)->nullable(false)->change();
        });
    }
};
