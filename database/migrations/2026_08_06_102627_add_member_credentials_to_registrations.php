<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The registration becomes the member account.
 *
 * The association asked for an email-and-password login rather than the emailed
 * one-time link the portal opened with. Rather than mint a second table of user
 * records to keep in step with the registrations, the registration itself gains
 * a password and is authenticated directly.
 *
 * Nullable on purpose: everyone who registered before this shipped has no
 * password, and must keep being able to get in by emailed link until they set
 * one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->rememberToken()->after('password');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');

            // An email address is now the thing you sign in with, so it has to
            // identify exactly one member. Safe to add: there are no
            // registrations yet, live or locally.
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn(['password', 'remember_token', 'last_login_at']);
        });
    }
};
