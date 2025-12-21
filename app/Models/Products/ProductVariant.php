<?php

namespace App\Models\Products;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\StockMovement;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock_quantity',
        'image_path',
        'is_enabled',
    ];

    /**
     * Check if there's enough stock for a given quantity
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    /**
     * Reduce stock quantity
     */
    public function reduceStock(int $quantity, string $reason = 'order', int $orderId = null, string $description = ''): void
    {
        $newQuantity = max(0, $this->stock_quantity - $quantity);
        $this->update(['stock_quantity' => $newQuantity]);

        // Log the stock movement
        $this->stockMovements()->create([
            'product_id' => $this->product->id, // Add product_id here
            'quantity' => $quantity,
            'movement_type' => 'out',
            'movement_reason' => $reason,
            'order_id' => $orderId,
            'description' => $description ?: "Stock reduced due to {$reason}",
        ]);
    }

    /**
     * Increase stock quantity
     */
    public function increaseStock(int $quantity, string $reason = 'restock', int $orderId = null, string $description = ''): void
    {
        $newQuantity = $this->stock_quantity + $quantity;
        $this->update(['stock_quantity' => $newQuantity]);

        // Log the stock movement
        $this->stockMovements()->create([
            'product_id' => $this->product->id, // Add product_id here
            'quantity' => $quantity,
            'movement_type' => 'in',
            'movement_reason' => $reason,
            'order_id' => $orderId,
            'description' => $description ?: "Stock increased due to {$reason}",
        ]);
    }

    /**
     * Get the stock movements for the product variant.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_variant_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(AttributeTerm::class, 'product_variant_terms');
    }
}
