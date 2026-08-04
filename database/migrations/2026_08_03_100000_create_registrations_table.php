<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mirrors "Registration Details" parts 1–7 one-to-one so the digital form
     * and the paper form stay auditable against each other.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            // Part 1 — Personal
            $table->string('full_name_en');
            $table->string('full_name_bn')->nullable();
            $table->string('blood_group', 8)->nullable();
            $table->string('mobile', 32);
            $table->string('whatsapp', 32)->nullable();
            $table->string('email');
            $table->text('present_address');
            $table->text('permanent_address')->nullable();

            // Part 2 — Academic
            $table->string('session', 32);
            $table->string('degree', 32);
            $table->string('class_roll', 64)->nullable();
            $table->string('registration_no', 64)->nullable();
            $table->unsignedSmallInteger('passing_year');

            // Part 3 — Professional
            $table->string('employment_status', 32);
            $table->string('profession')->nullable();
            $table->string('designation')->nullable();
            $table->string('organization')->nullable();

            // Part 4 — Reunion & event
            $table->string('tshirt_size', 8);
            $table->boolean('cultural_program')->default(false);
            $table->string('guest_count', 8)->default('0');
            $table->json('guests')->nullable();

            // Part 5 — Memories
            $table->text('memories')->nullable();

            // Part 6 — Photo
            $table->string('photo_path')->nullable();

            // Part 7 — Payment (manually verified)
            $table->string('payment_method', 32);
            $table->string('transaction_id', 64);
            $table->string('sender_number', 32);
            $table->unsignedInteger('amount_paid');
            $table->unsignedInteger('amount_due')->default(0);
            $table->string('payment_status', 16)->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index('payment_status');
            $table->index('passing_year');
            $table->index('email');
            // One paid seat per transaction — blocks accidental double submits.
            $table->unique(['payment_method', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
