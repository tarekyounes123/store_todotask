<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products
        $products = Product::all();

        foreach ($products as $product) {
            // Create variants only for some products to test the functionality
            if (in_array($product->name, ['Stylish T-Shirt', 'Smartphone X', 'Yoga Mat Pro'])) {
                $variants = [];
                
                if ($product->name === 'Stylish T-Shirt') {
                    // Create variants for T-Shirt with size and color options
                    $variants = [
                        [
                            'sku' => 'TSHIRT-S-RED-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 50,
                            'attributes' => json_encode(['size' => 'S', 'color' => 'Red']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'TSHIRT-M-RED-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 45,
                            'attributes' => json_encode(['size' => 'M', 'color' => 'Red']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'TSHIRT-L-RED-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 40,
                            'attributes' => json_encode(['size' => 'L', 'color' => 'Red']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'TSHIRT-S-BLUE-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 35,
                            'attributes' => json_encode(['size' => 'S', 'color' => 'Blue']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'TSHIRT-M-BLUE-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 30,
                            'attributes' => json_encode(['size' => 'M', 'color' => 'Blue']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'TSHIRT-L-BLUE-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 25,
                            'attributes' => json_encode(['size' => 'L', 'color' => 'Blue']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'TSHIRT-S-GRN-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 20,
                            'attributes' => json_encode(['size' => 'S', 'color' => 'Green']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'TSHIRT-M-GRN-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 15,
                            'attributes' => json_encode(['size' => 'M', 'color' => 'Green']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'TSHIRT-L-GRN-' . Str::random(6),
                            'price' => 18.99,
                            'stock_quantity' => 10,
                            'attributes' => json_encode(['size' => 'L', 'color' => 'Green']),
                            'is_active' => true
                        ]
                    ];
                } elseif ($product->name === 'Smartphone X') {
                    // Create variants for Smartphone with storage options
                    $variants = [
                        [
                            'sku' => 'PHN-128GB-BLK-' . Str::random(6),
                            'price' => 799.99,
                            'stock_quantity' => 20,
                            'attributes' => json_encode(['storage' => '128GB', 'color' => 'Black']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'PHN-256GB-BLK-' . Str::random(6),
                            'price' => 899.99,
                            'stock_quantity' => 15,
                            'attributes' => json_encode(['storage' => '256GB', 'color' => 'Black']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'PHN-512GB-BLK-' . Str::random(6),
                            'price' => 999.99,
                            'stock_quantity' => 10,
                            'attributes' => json_encode(['storage' => '512GB', 'color' => 'Black']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'PHN-128GB-WHT-' . Str::random(6),
                            'price' => 799.99,
                            'stock_quantity' => 18,
                            'attributes' => json_encode(['storage' => '128GB', 'color' => 'White']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'PHN-256GB-WHT-' . Str::random(6),
                            'price' => 899.99,
                            'stock_quantity' => 12,
                            'attributes' => json_encode(['storage' => '256GB', 'color' => 'White']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'PHN-512GB-WHT-' . Str::random(6),
                            'price' => 999.99,
                            'stock_quantity' => 8,
                            'attributes' => json_encode(['storage' => '512GB', 'color' => 'White']),
                            'is_active' => true
                        ]
                    ];
                } elseif ($product->name === 'Yoga Mat Pro') {
                    // Create variants for Yoga Mat with thickness options
                    $variants = [
                        [
                            'sku' => 'YGM-4MM-GRN-' . Str::random(6),
                            'price' => 45.00,
                            'stock_quantity' => 30,
                            'attributes' => json_encode(['thickness' => '4mm', 'color' => 'Green']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'YGM-6MM-GRN-' . Str::random(6),
                            'price' => 48.00,
                            'stock_quantity' => 25,
                            'attributes' => json_encode(['thickness' => '6mm', 'color' => 'Green']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'YGM-8MM-GRN-' . Str::random(6),
                            'price' => 51.00,
                            'stock_quantity' => 20,
                            'attributes' => json_encode(['thickness' => '8mm', 'color' => 'Green']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'YGM-4MM-PUR-' . Str::random(6),
                            'price' => 45.00,
                            'stock_quantity' => 28,
                            'attributes' => json_encode(['thickness' => '4mm', 'color' => 'Purple']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'YGM-6MM-PUR-' . Str::random(6),
                            'price' => 48.00,
                            'stock_quantity' => 22,
                            'attributes' => json_encode(['thickness' => '6mm', 'color' => 'Purple']),
                            'is_active' => true
                        ],
                        [
                            'sku' => 'YGM-8MM-PUR-' . Str::random(6),
                            'price' => 51.00,
                            'stock_quantity' => 18,
                            'attributes' => json_encode(['thickness' => '8mm', 'color' => 'Purple']),
                            'is_active' => true
                        ]
                    ];
                }

                // Create the variants
                foreach ($variants as $variantData) {
                    $product->variants()->create($variantData);
                }
            }
        }
    }
}