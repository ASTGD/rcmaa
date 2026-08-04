<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            // alumni | reunion_convening | reunion_sub | advisory | batch_rep | ex_student
            $table->string('committee', 40)->index();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('designation');
            $table->string('designation_bn')->nullable();
            $table->string('batch')->nullable();
            $table->string('profession')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('email')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation');
            $table->string('qualification')->nullable();
            $table->string('specialisation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_head')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->string('start_time', 32)->nullable();
            $table->string('venue')->nullable();
            $table->string('cover_path')->nullable();
            $table->boolean('registration_open')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('starts_on');
        });

        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('attachment_path')->nullable();
            $table->date('published_on');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('published_on');
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('caption')->nullable();
            $table->string('category', 40)->default('campus')->index();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category', 40)->default('general')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tier', 24)->default('partner');
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 32)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('events');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('committee_members');
    }
};
