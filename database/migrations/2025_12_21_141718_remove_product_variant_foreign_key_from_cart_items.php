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
        // Foreign key constraint has already been removed by the previous migration
        // This migration is now a no-op to mark it as run
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
        });
    }
};
