<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock_quantity',
        'attributes',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'attributes' => 'array',
    ];

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the effective price (use variant price if set, otherwise product default price)
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->price ?? $this->product->price;
    }

    /**
     * Get a specific attribute value from the JSON attributes field
     */
    public function getAttributeFromJson(string $key): ?string
    {
        $attrs = is_string($this->attributes) ? json_decode($this->attributes, true) : $this->attributes;
        return $attrs[$key] ?? null;
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0 && $this->is_active;
    }
}
