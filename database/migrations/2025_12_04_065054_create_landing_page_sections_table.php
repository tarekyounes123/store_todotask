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
        Schema::create('landing_page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the section (e.g., 'hero', 'features', 'products')
            $table->string('title')->nullable(); // Section title
            $table->text('content')->nullable(); // Section content (HTML or JSON)
            $table->string('section_type'); // Type of the section (hero, features, products, cta, etc.)
            $table->json('settings')->nullable(); // Additional settings for the section (styling, etc.)
            $table->integer('position')->default(0); // Position of the section in the page
            $table->boolean('is_active')->default(true); // Whether the section is visible
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_sections');
    }
};
