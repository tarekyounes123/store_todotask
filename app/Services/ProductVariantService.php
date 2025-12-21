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
            // Store IDs of variants that are being processed (updated or created)
            $processedVariantIds = [];

            // --- 1. Process Product Attributes ---
            // Clear existing product attributes to re-sync
            $product->attributes()->delete();
            $variantAttributeTermIds = []; // Stores attribute_term_id for variant combinations

            foreach ($data['attributes'] ?? [] as $attributeData) {
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
                foreach ($attributeData['terms'] ?? [] as $termValue) {
                    // Find or create the global attribute term
                    $term = AttributeTerm::firstOrCreate(
                        [
                            'attribute_id' => $attribute->id,
                            'slug' => Str::slug($termValue['value'])
                        ],
                        [
                            'value' => $termValue['value'] ?? $termValue
                        ]
                    );
                    $termIds[] = $term->id;
                }

                // Attach terms to the product attribute
                $productAttribute->terms()->createMany(
                    collect($termIds)->map(fn($id) => ['attribute_term_id' => $id])->all()
                );

                if ($isVariantAttribute) {
                    $variantAttributeTermIds = array_merge($variantAttributeTermIds, $termIds);
                }
            }

            // --- 2. Process Product Variants ---
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $existingVariant = null;

                    // Try to find variant by ID first (for existing variants being updated)
                    if (isset($variantData['id']) && !Str::startsWith($variantData['id'], 'new-')) {
                        $existingVariant = $product->variants()->find($variantData['id']);
                    }

                    // Process terms for the current variant data to ensure valid attribute_term_ids
                    $actualVariantTermIds = [];
                    foreach ($variantData['terms'] ?? [] as $termInput) {
                        $termId = $termInput['attribute_term_id'] ?? null;
                        $termValue = $termInput['value'] ?? null;
                        $attributeId = $termInput['attribute_id'] ?? null;

                        if ($termId) {
                            // If we have an ID, use it (it's an existing term)
                            $actualVariantTermIds[] = $termId;
                        } elseif ($termValue !== null && $attributeId !== null) {
                            // If we don't have an ID but have value and attribute_id, find or create the term
                            $term = AttributeTerm::firstOrCreate(
                                [
                                    'attribute_id' => $attributeId,
                                    'slug' => Str::slug($termValue)
                                ],
                                [
                                    'value' => $termValue
                                ]
                            );
                            $actualVariantTermIds[] = $term->id;
                        } else {
                            // This case indicates an invalid term input, possibly a new term without sufficient data
                            \Log::warning("Skipping invalid variant term input: " . json_encode($termInput));
                        }
                    }

                    // Make sure terms are unique and sorted for consistent matching
                    $actualVariantTermIds = collect($actualVariantTermIds)->unique()->sort()->values()->all();

                    // If not found by ID, try to find by terms (for newly generated variants or if ID match failed)
                    // Only try term matching if we didn't find the variant by ID
                    if (!$existingVariant) {
                        // Look for an existing variant with the same combination of terms
                        $existingVariant = $this->findVariantByTerms($product, $actualVariantTermIds);
                    }

                    if ($existingVariant) {
                        // Update existing variant
                        $this->syncVariantData($existingVariant, $variantData);
                        // Sync terms to ensure any changes are reflected
                        $existingVariant->terms()->sync($actualVariantTermIds);
                        $processedVariantIds[] = $existingVariant->id;
                    } else {
                        // Create new variant
                        $newVariant = $product->variants()->create([
                            'price' => $variantData['price'],
                            'sku' => $variantData['sku'] ?? null,
                            'stock_quantity' => $variantData['stock_quantity'],
                            'image_path' => $variantData['image_path'] ?? null,
                            'is_enabled' => $variantData['is_enabled'] ?? true,
                        ]);
                        $newVariant->terms()->attach($actualVariantTermIds);
                        $processedVariantIds[] = $newVariant->id;
                    }
                }
            }

            // --- 3. Delete Variants Not Present in the Request ---
            // Get all current variant IDs for this product
            $currentProductVariantIds = $product->variants()->pluck('id')->toArray();

            // Find variants that were not processed (i.e., not in $processedVariantIds)
            $variantsToDelete = array_diff($currentProductVariantIds, $processedVariantIds);

            if (!empty($variantsToDelete)) {
                ProductVariant::whereIn('id', $variantsToDelete)->delete();
            }

        });
    }

    /**
     * Find a variant by its associated term IDs
     *
     * @param Product $product
     * @param array $termIds
     * @return ProductVariant|null
     */
    protected function findVariantByTerms(Product $product, array $termIds): ?ProductVariant
    {
        $termIds = collect($termIds)->sort()->values()->all();

        foreach ($product->variants as $variant) {
            $variantTermIds = $variant->terms->pluck('id')->sort()->values()->all();

            if ($variantTermIds === $termIds) {
                return $variant;
            }
        }

        return null;
    }

    public function syncVariantData(ProductVariant $variant, array $data)
    {
        $variant->update([
            'price' => $data['price'],
            'sku' => $data['sku'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'image_path' => $data['image_path'] ?? null,
            'is_enabled' => $data['is_enabled'] ?? true,
        ]);
    }
}
