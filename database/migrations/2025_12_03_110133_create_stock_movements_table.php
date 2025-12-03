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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('movement_type', ['in', 'out']); // in for restocking, out for sales/orders
            $table->string('movement_reason'); // e.g. 'order', 'restock', 'adjustment', 'cancelled_order'
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null'); // Reference to order if applicable
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who made the change
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
