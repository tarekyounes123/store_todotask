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
        // Log the incoming data to see what's being received from the UI
        \Log::info('ProductVariantService createOrUpdateVariants received data:', [
            'product_id' => $product->id,
            'data_keys' => array_keys($data),
            'variants_count' => isset($data['variants']) ? count($data['variants']) : 0,
            'variants_sample' => isset($data['variants']) ? array_slice($data['variants'], 0, 2, true) : null
        ]);

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
                    // Determine the actual value based on whether it's an array or string
                    $actualValue = is_array($termValue) ? ($termValue['value'] ?? (is_string($termValue) ? $termValue : '')) : $termValue;
                    // Ensure $actualValue is a string for the slug
                    $actualValue = is_string($actualValue) ? $actualValue : (string) $actualValue;
                    // Find or create the global attribute term
                    $term = AttributeTerm::firstOrCreate(
                        [
                            'attribute_id' => $attribute->id,
                            'slug' => Str::slug($actualValue)
                        ],
                        [
                            'value' => $actualValue
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
                \Log::info('Processing variants:', [
                    'variant_keys' => array_keys($data['variants']),
                    'first_variant_sample' => isset($data['variants']) && count($data['variants']) > 0 ? reset($data['variants']) : null
                ]);

                foreach ($data['variants'] as $variantId => $variantData) {
                    \Log::info('Processing individual variant:', [
                        'variant_id' => $variantId,
                        'variant_data' => $variantData
                    ]);

                    $existingVariant = null;

                    // The $variantId is the array key which should be the actual variant ID from the form
                    // Check if this is an existing variant by looking at the key (which is the ID)
                    if ($variantId && !Str::startsWith($variantId, 'new-')) {
                        // Find the exact variant by ID to ensure we're updating the correct one
                        // This is the critical fix - we must ensure we're updating the right variant
                        $existingVariant = $product->variants()
                            ->where('id', $variantId)
                            ->where('product_id', $product->id) // Additional safety check
                            ->first();

                        \Log::info('Found existing variant:', [
                            'variant_id' => $variantId,
                            'existing_variant_found' => $existingVariant !== null
                        ]);
                    }

                    // Process terms for the current variant data to ensure valid attribute_term_ids
                    $actualVariantTermIds = [];

                    // Handle different formats of term data that might come from the form
                    if (isset($variantData['terms'])) {
                        \Log::info('Processing terms for variant:', [
                            'variant_id' => $variantId,
                            'terms_data' => $variantData['terms']
                        ]);

                        foreach ($variantData['terms'] as $termInput) {
                            // Check if $termInput is an array (structured format) or a simple ID (flat format)
                            if (is_array($termInput)) {
                                // Structured format: ['attribute_term_id' => id, 'value' => val, 'attribute_id' => id]
                                $termId = $termInput['attribute_term_id'] ?? null;
                                $termValue = $termInput['value'] ?? null;
                                $attributeId = $termInput['attribute_id'] ?? null;

                                if ($termId) {
                                    // If we have an ID, use it (it's an existing term)
                                    $actualVariantTermIds[] = $termId;
                                } elseif ($termValue !== null && $attributeId !== null) {
                                    // If we don't have an ID but have value and attribute_id, find or create the term
                                    // Ensure $termValue is a string for the slug
                                    $termValueString = is_string($termValue) ? $termValue : (string) $termValue;
                                    $term = AttributeTerm::firstOrCreate(
                                        [
                                            'attribute_id' => $attributeId,
                                            'slug' => Str::slug($termValueString)
                                        ],
                                        [
                                            'value' => $termValueString
                                        ]
                                    );
                                    $actualVariantTermIds[] = $term->id;
                                } else {
                                    // This case indicates an invalid term input, possibly a new term without sufficient data
                                    \Log::warning("Skipping invalid variant term input: " . json_encode($termInput));
                                }
                            } else {
                                // Flat format: just the term ID (this is how existing variants are sent from the form)
                                // This handles the case where terms are sent as a simple array of IDs
                                if (is_numeric($termInput) || is_string($termInput)) {
                                    $actualVariantTermIds[] = (int) $termInput;
                                }
                            }
                        }
                    }

                    // Make sure terms are unique and sorted for consistent matching
                    $actualVariantTermIds = collect($actualVariantTermIds)->unique()->sort()->values()->all();

                    \Log::info('Final terms for variant:', [
                        'variant_id' => $variantId,
                        'actualVariantTermIds' => $actualVariantTermIds
                    ]);

                    if ($existingVariant) {
                        // Update the existing variant directly using its ID - this is the key fix
                        $this->syncVariantData($existingVariant, $variantData);

                        // Update the terms for the existing variant
                        // Since we now properly handle both formats (structured and flat), $actualVariantTermIds should contain the correct terms
                        if (!empty($actualVariantTermIds)) {
                            $existingVariant->terms()->sync($actualVariantTermIds);
                            \Log::info('Synced terms for existing variant:', [
                                'variant_id' => $variantId,
                                'synced_terms' => $actualVariantTermIds
                            ]);
                        } else {
                            \Log::warning('No terms to sync for existing variant:', ['variant_id' => $variantId]);
                        }
                        $processedVariantIds[] = $existingVariant->id;
                    } else {
                        // Create new variant if no existing one was found by ID
                        $newVariant = $product->variants()->create([
                            'price' => $variantData['price'],
                            'sku' => $variantData['sku'] ?? null,
                            'stock_quantity' => $variantData['stock_quantity'],
                            'image_path' => $variantData['image_path'] ?? null,
                            'is_enabled' => $variantData['is_enabled'] ?? true,
                        ]);

                        if (!empty($actualVariantTermIds)) {
                            $newVariant->terms()->attach($actualVariantTermIds);
                        }
                        $processedVariantIds[] = $newVariant->id;

                        \Log::info('Created new variant:', [
                            'new_variant_id' => $newVariant->id,
                            'variant_data' => $variantData
                        ]);
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
        $updateData = [
            'price' => $data['price'],
            'sku' => $data['sku'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'image_path' => $data['image_path'] ?? null,
        ];

        // Only update is_enabled if it's explicitly provided in the request data
        // Otherwise, preserve the existing value to prevent unintended changes
        if (array_key_exists('is_enabled', $data)) {
            $updateData['is_enabled'] = (bool) $data['is_enabled'];
        }

        $variant->update($updateData);
    }
}
