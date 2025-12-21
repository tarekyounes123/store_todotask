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
        // Use raw SQL to drop the foreign key constraint for product_variant_id
        $sql = "SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_NAME = 'cart_items'
                AND COLUMN_NAME = 'product_variant_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL";

        $result = \DB::select($sql);

        if (!empty($result)) {
            $constraintName = $result[0]->CONSTRAINT_NAME;
            \DB::statement("ALTER TABLE cart_items DROP FOREIGN KEY {$constraintName}");
        }
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
