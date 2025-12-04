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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique(); // Key for the setting (e.g., 'footer_content', 'site_name', etc.)
            $table->json('setting_value')->nullable(); // JSON value for the setting
            $table->text('description')->nullable(); // Description of what the setting is for
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
