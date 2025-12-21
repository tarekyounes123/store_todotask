<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_attribute_id',
        'attribute_term_id',
        'value',
    ];

    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function attributeTerm(): BelongsTo
    {
        return $this->belongsTo(AttributeTerm::class);
    }
}
