<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'attribute_term_id',
    ];

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function attributeTerm(): BelongsTo
    {
        return $this->belongsTo(AttributeTerm::class);
    }
}
