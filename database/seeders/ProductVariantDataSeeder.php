<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Products\Attribute;
use App\Models\Products\AttributeTerm;
use App\Models\Products\ProductAttribute;
use App\Models\Products\ProductAttributeTerm;
use App\Models\Products\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductVariantDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow deletions/inserts in any order
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear new variant tables to ensure idempotence
        ProductVariant::truncate();
        ProductAttributeTerm::truncate();
        ProductAttribute::truncate();
        AttributeTerm::truncate();
        Attribute::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $oldVariants = DB::table('product_variants_old')->get();

        foreach ($oldVariants as $oldVariant) {
            $product = Product::find($oldVariant->product_id);

            if (!$product) {
                continue; // Skip if product not found
            }

            // Create new ProductVariant
            $newVariant = ProductVariant::create([
                'product_id' => $oldVariant->product_id,
                'sku' => $oldVariant->sku,
                'price' => $oldVariant->price,
                'stock_quantity' => $oldVariant->stock_quantity,
                // 'image_path' => $oldVariant->image_path, // Assuming no image path in old variants
                'is_enabled' => $oldVariant->is_active,
                'created_at' => $oldVariant->created_at,
                'updated_at' => $oldVariant->updated_at,
            ]);

            $attributesData = json_decode($oldVariant->attributes, true);

            if (empty($attributesData)) {
                continue;
            }

            $variantTermsToAttach = [];

            foreach ($attributesData as $attributeName => $termValue) {
                // Find or create global attribute
                $attribute = Attribute::firstOrCreate(
                    ['slug' => Str::slug($attributeName)],
                    ['name' => $attributeName]
                );

                // Find or create global attribute term
                $attributeTerm = AttributeTerm::firstOrCreate(
                    ['attribute_id' => $attribute->id, 'slug' => Str::slug($termValue)],
                    ['value' => $termValue]
                );

                // Create ProductAttribute (assuming all attributes in old variants are variant attributes)
                $productAttribute = ProductAttribute::firstOrCreate(
                    ['product_id' => $product->id, 'attribute_id' => $attribute->id],
                    ['name' => $attribute->name, 'is_variant_attribute' => true]
                );

                // Create ProductAttributeTerm
                ProductAttributeTerm::firstOrCreate(
                    ['product_attribute_id' => $productAttribute->id, 'attribute_term_id' => $attributeTerm->id]
                );

                $variantTermsToAttach[] = $attributeTerm->id;
            }
            // Attach terms to the new ProductVariant
            $newVariant->terms()->attach($variantTermsToAttach);
        }
    }
}