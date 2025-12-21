<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Products\Attribute;
use App\Models\Products\AttributeTerm;
use App\Models\Products\ProductVariant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductVariantService
{
    /**
     * Generate all possible combinations of a given array of arrays.
     *
     * @param array $arrays
     * @return array
     */
    protected function generateCombinations(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $key => $values) {
            $tmp = [];
            foreach ($result as $combination) {
                foreach ($values as $value) {
                    $tmp[] = $combination + [$key => $value];
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    /**
     * @param Product $product
     * @param array $data
     * @return void
     * @throws \Throwable
     */
    public function createOrUpdateVariants(Product $product, array $data): void
    {
        DB::transaction(function () use ($product, $data) {
            $product->attributes()->delete();
            $product->variants()->delete();

            $variantAttributeIds = [];

            foreach ($data['attributes'] as $attributeData) {
                $attribute = Attribute::find($attributeData['attribute_id']);
                if (!$attribute) {
                    continue;
                }

                $isVariantAttribute = $attributeData['is_variant_attribute'] ?? false;

                $productAttribute = $product->attributes()->create([
                    'attribute_id' => $attribute->id,
                    'name' => $attribute->name,
                    'is_variant_attribute' => $isVariantAttribute,
                ]);

                $termIds = [];
                foreach ($attributeData['terms'] as $termValue) {
                    $term = AttributeTerm::firstOrCreate(
                        [
                            'attribute_id' => $attribute->id,
                            'slug' => Str::slug($termValue)
                        ],
                        [
                            'value' => $termValue
                        ]
                    );
                    $termIds[] = $term->id;
                }

                $productAttribute->terms()->createMany(
                    collect($termIds)->map(fn($id) => ['attribute_term_id' => $id])->all()
                );


                if ($isVariantAttribute) {
                    $variantAttributeIds[$attribute->id] = $termIds;
                }
            }

            if (empty($variantAttributeIds)) {
                return;
            }

            $combinations = $this->generateCombinations($variantAttributeIds);

            foreach ($combinations as $combination) {
                $variant = $product->variants()->create([
                    'price' => $product->price,
                    'stock_quantity' => $product->stock_quantity,
                ]);

                $variant->terms()->attach(array_values($combination));
            }
        });
    }

    public function syncVariantData(ProductVariant $variant, array $data)
    {
        $variant->update([
            'price' => $data['price'],
            'sku' => $data['sku'],
            'stock_quantity' => $data['stock_quantity'],
        ]);
    }
}
