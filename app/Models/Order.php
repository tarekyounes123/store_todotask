<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'special_instructions',
        'shipped_at',
        'delivered_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Restore stock quantities when order is cancelled
     */
    public function restoreStock(): void
    {
        foreach ($this->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->increaseStock($item->quantity, 'cancelled_order', $this->id, "Stock restored from cancelled order #{$this->order_number}");
            }
        }
    }

    /**
     * Reduce stock from products when order status changes from cancelled to another status.
     */
    public function reduceStockFromCancelled(): void
    {
        foreach ($this->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->reduceStock($item->quantity, 'order', $this->id, "Stock reduced (order reactivated from cancelled status) #{$this->order_number}");
            }
        }
    }

}
