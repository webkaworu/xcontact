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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('recipient_email');
            $table->foreignId('notification_template_id')->nullable()->constrained('email_templates')->onDelete('set null');
            $table->boolean('auto_reply_enabled')->default(false);
            $table->foreignId('auto_reply_template_id')->nullable()->constrained('email_templates')->onDelete('set null');
            $table->integer('daily_limit')->nullable()->default(5);
            $table->integer('monthly_limit')->nullable()->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};