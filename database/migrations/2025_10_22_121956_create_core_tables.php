<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('form_limit')->nullable();
            $table->integer('monthly_limit')->nullable();
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['notification', 'auto_reply']);
            $table->string('subject');
            $table->text('body');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('recipient_email');
            $table->foreignId('notification_template_id')->constrained('email_templates')->onDelete('cascade');
            $table->boolean('auto_reply_enabled')->default(false);
            $table->foreignId('auto_reply_template_id')->nullable()->constrained('email_templates')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->json('data');
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });

        Schema::create('access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_tokens');
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('plans');
    }
};
