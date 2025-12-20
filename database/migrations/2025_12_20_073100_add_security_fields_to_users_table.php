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
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('password');
            }
            if (!Schema::hasColumn('users', 'suspended_until')) {
                $table->timestamp('suspended_until')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('suspended_until');
            }
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('last_login_at');
            }
        });

        // Update the password_reset_tokens table to add 'used' and 'expires_at' columns
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_tokens', 'used')) {
                $table->boolean('used')->default(false)->after('token');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('used');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'suspended_until',
                'last_login_at',
                'google_id'
            ]);
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn(['used', 'expires_at']);
        });
    }
};
