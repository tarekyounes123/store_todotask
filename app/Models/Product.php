<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'buy_price',
        'stock_quantity',
        'category_id',
        'slug',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'buy_price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    // Reviews and ratings relationships
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Calculate average rating
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Calculate total number of reviews
    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * The users who have favorited this product.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_product_favorites', 'product_id', 'user_id');
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Determine if the current user has favorited this product.
     */
    public function getIsFavoritedByUserAttribute(): bool
    {
        if (auth()->guest()) {
            return false;
        }
        // Check if the 'favoritedBy' relationship is loaded and contains the current user
        // This is more efficient if the relationship is already constrained and loaded
        if ($this->relationLoaded('favoritedBy')) {
            return $this->favoritedBy->contains(auth()->id());
        }

        // Otherwise, query the database directly
        return $this->favoritedBy()->where('user_id', auth()->id())->exists();
    }

    /**
     * Check if the product is in stock
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

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
            'quantity' => $quantity,
            'movement_type' => 'in',
            'movement_reason' => $reason,
            'order_id' => $orderId,
            'description' => $description ?: "Stock increased due to {$reason}",
        ]);
    }

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get profit per unit
     */
    public function getProfitPerUnitAttribute(): float
    {
        $buyPrice = $this->getAttribute('buy_price');
        if ($buyPrice === null || $buyPrice === '') {
            return 0.0;
        }

        return max(0, $this->price - $buyPrice);
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginAttribute(): float
    {
        $buyPrice = $this->getAttribute('buy_price');
        if ($buyPrice === null || $buyPrice === '' || $buyPrice == 0) {
            return 0.0;
        }

        $profitPerUnit = $this->getProfitPerUnitAttribute();
        return $buyPrice > 0 ? ($profitPerUnit / $buyPrice) * 100 : 0.0;
    }
}