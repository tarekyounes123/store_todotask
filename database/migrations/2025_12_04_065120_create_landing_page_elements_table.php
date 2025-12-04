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
        Schema::create('landing_page_elements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the element
            $table->string('element_type'); // Type of element (text, image, button, etc.)
            $table->text('content')->nullable(); // Content of the element
            $table->json('attributes')->nullable(); // Additional attributes for the element
            $table->integer('position')->default(0); // Position within the section
            $table->unsignedBigInteger('section_id')->nullable(); // Reference to parent section
            $table->boolean('is_active')->default(true); // Whether the element is visible
            $table->json('settings')->nullable(); // Additional settings for the element (styling, etc.)
            $table->timestamps();

            $table->foreign('section_id')->references('id')->on('landing_page_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_elements');
    }
};
