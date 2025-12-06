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
        // Only add columns if they don't already exist
        if (!Schema::hasColumn('password_reset_tokens', 'used')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->boolean('used')->default(false);
            });
        }

        if (!Schema::hasColumn('password_reset_tokens', 'expires_at')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable();
            });
        }

        if (!Schema::hasColumn('password_reset_tokens', 'token') ||
            Schema::getColumnType('password_reset_tokens', 'token') !== 'text') {
            // If the token column exists but is string type, we may need to update it
            // But changing column types requires doctrine/dbal package, so we'll leave it as is
        }

        if (!Schema::hasIndex('password_reset_tokens', 'password_reset_tokens_email_index')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->index('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropIndex(['email']); // Drop index if it exists
            $table->dropColumn(['used', 'expires_at']);
        });
    }
};
