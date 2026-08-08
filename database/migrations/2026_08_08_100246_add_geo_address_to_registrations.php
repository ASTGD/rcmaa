<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Structured place-of-residence, on top of the free-text addresses.
 *
 * The association wants the directory searchable by location, and a textarea
 * cannot be filtered. District and upazila/thana become dropdowns on the form
 * (config/bd-geo.php) and columns here; the textarea stays for the part of an
 * address that genuinely is free text.
 *
 * Nullable throughout: rows registered before this shipped have no structured
 * location, and the permanent address as a whole is optional.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('present_district', 64)->nullable()->after('present_address');
            $table->string('present_upazila', 64)->nullable()->after('present_district');
            $table->string('permanent_district', 64)->nullable()->after('permanent_address');
            $table->string('permanent_upazila', 64)->nullable()->after('permanent_district');

            // The directory filters on this one.
            $table->index('present_district');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex(['present_district']);
            $table->dropColumn([
                'present_district', 'present_upazila',
                'permanent_district', 'permanent_upazila',
            ]);
        });
    }
};
