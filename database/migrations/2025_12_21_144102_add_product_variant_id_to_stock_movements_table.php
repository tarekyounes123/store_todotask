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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                  ->nullable()
                  ->constrained('product_variants')
                  ->onDelete('set null')
                  ->after('product_id'); // Place it after product_id for logical grouping
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};
