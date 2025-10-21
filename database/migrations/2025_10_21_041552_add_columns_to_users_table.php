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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('form_creation_limit')->nullable()->default(10)->after('password');
            $table->foreignId('registration_token_id')->nullable()->constrained('registration_tokens')->onDelete('set null')->after('form_creation_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['registration_token_id']);
            $table->dropColumn('registration_token_id');
            $table->dropColumn('form_creation_limit');
        });
    }
};