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
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the old foreign key constraint by its explicit name
            $table->dropForeign('order_items_product_variant_id_foreign');

            // Add the new foreign key constraint referencing the correct 'product_variants' table
            $table->foreign('product_variant_id')
                  ->references('id')
                  ->on('product_variants')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the foreign key added in the 'up' method
            $table->dropForeign(['product_variant_id']);
        });
    }
};
