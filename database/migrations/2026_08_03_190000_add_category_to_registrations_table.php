<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registration is priced by category (teacher / alumni / recent graduate /
     * current student), each with its own fee, and only the first two may bring
     * guests. Recorded per registration so a later fee change cannot rewrite
     * what somebody was actually charged.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('category', 32)->default('alumni')->after('reference')->index();
            $table->unsignedInteger('category_fee')->default(0)->after('amount_due');
            $table->unsignedInteger('guest_fee')->default(0)->after('category_fee');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['category', 'category_fee', 'guest_fee']);
        });
    }
};
