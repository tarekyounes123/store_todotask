<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'total',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items for the cart.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate the subtotal for the cart
     */
    public function getSubtotalAttribute(): float
    {
        $subtotal = 0;
        foreach ($this->cartItems as $item) {
            $subtotal += $item->price * $item->quantity;
        }
        return $subtotal;
    }

    /**
     * Calculate the shipping cost for the cart
     * Free shipping for orders over $50, $5 shipping otherwise (with minimum order of $0.01)
     */
    public function getShippingCostAttribute(): float
    {
        $subtotal = $this->subtotal;
        return $subtotal < 50 && $subtotal > 0 ? 5.00 : 0.00;
    }

    /**
     * Calculate the total for the cart (subtotal + shipping)
     */
    public function getTotalAttribute(): float
    {
        // If we're accessing the total attribute, calculate it dynamically
        return $this->subtotal + $this->shipping_cost;
    }
}
