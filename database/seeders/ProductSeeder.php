<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing storage for product images
        Storage::disk('public')->deleteDirectory('products');
        Storage::disk('public')->makeDirectory('products');

        $categories = Category::all();

        // Ensure categories exist before creating products
        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        $productsData = [
            [
                'name' => 'Smartphone X',
                'description' => 'A powerful smartphone with a great camera and long-lasting battery.',
                'price' => 799.99,
                'category' => 'Electronics',
                'images' => ['phone1.jpg', 'phone2.jpg']
            ],
            [
                'name' => 'Eloquent JavaScript',
                'description' => 'A modern introduction to programming.',
                'price' => 25.00,
                'category' => 'Books',
                'images' => ['book1.jpg']
            ],
            [
                'name' => 'Smart Coffee Maker',
                'description' => 'Brew your coffee with a touch of a button from anywhere.',
                'price' => 120.50,
                'category' => 'Home & Kitchen',
                'images' => ['coffeemaker1.jpg']
            ],
            [
                'name' => 'Stylish T-Shirt',
                'description' => 'Comfortable and fashionable cotton t-shirt.',
                'price' => 19.99,
                'category' => 'Fashion',
                'images' => ['tshirt1.jpg', 'tshirt2.jpg']
            ],
            [
                'name' => 'Yoga Mat Pro',
                'description' => 'High-quality, non-slip yoga mat for all your fitness needs.',
                'price' => 45.00,
                'category' => 'Sports & Outdoors',
                'images' => ['yogamat1.jpg']
            ],
            [
                'name' => 'Luxury Face Cream',
                'description' => 'Rejuvenate your skin with this hydrating face cream.',
                'price' => 85.00,
                'category' => 'Beauty & Personal Care',
                'images' => ['cream1.jpg']
            ],
        ];

        foreach ($productsData as $productData) {
            $category = $categories->where('name', $productData['category'])->first();

            if ($category) {
                $product = Product::create([
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'category_id' => $category->id,
                ]);

                foreach ($productData['images'] as $imageName) {
                    // Create a dummy image file if it doesn't exist
                    $dummyImagePath = database_path('seeders/dummy_images/' . $imageName);
                    if (!file_exists($dummyImagePath)) {
                        if (!is_dir(dirname($dummyImagePath))) {
                            mkdir(dirname($dummyImagePath), 0777, true);
                        }
                        // Create a simple text file as a placeholder for image
                        file_put_contents($dummyImagePath, 'Dummy image content for ' . $imageName);
                    }

                    // Use Storage facade to put the file
                    $path = Storage::disk('public')->putFileAs(
                        'products',
                        new \Illuminate\Http\File($dummyImagePath),
                        $imageName
                    );

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                    ]);
                }
            }
        }
    }
}
