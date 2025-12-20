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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique(); // Stock Keeping Unit - unique identifier for the variant
            $table->decimal('price', 8, 2)->nullable(); // Nullable to allow using product default price
            $table->integer('stock_quantity')->default(0);
            $table->json('attributes')->default('{}'); // Store variant attributes like size, color, etc.
            $table->boolean('is_active')->default(true); // Whether the variant is available for purchase
            $table->timestamps();

            // Ensure SKU is unique across all variants
            $table->index(['product_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
