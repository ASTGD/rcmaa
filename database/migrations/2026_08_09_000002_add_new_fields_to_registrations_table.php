<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('linkedin_url')->nullable()->after('whatsapp');
            $table->string('profession_type')->nullable()->after('linkedin_url');
            $table->string('work_location')->nullable()->after('profession_type');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['linkedin_url', 'profession_type', 'work_location']);
        });
    }
};
