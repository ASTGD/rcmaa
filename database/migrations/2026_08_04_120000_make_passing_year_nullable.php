<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Current students have not passed yet, so there is no year to give. The column
 * was NOT NULL, which made the question unanswerable for category 4.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unsignedSmallInteger('passing_year')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unsignedSmallInteger('passing_year')->nullable(false)->change();
        });
    }
};
