<?php

namespace App\Http\Controllers;

use App\Models\Category; // Added
use App\Models\Product;
use Illuminate\Http\Request;

class PublicProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'images');

        // Eager load favoritedBy relationship for the authenticated user
        if (auth()->check()) {
            $query->withExists(['favoritedBy as is_favorited_by_user' => function ($query) {
                $query->where('user_id', auth()->id());
            }]);
        }

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply Category Filter
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Apply Price Range Filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // Apply Sorting
        if ($request->filled('sort_by')) {
            switch ($request->input('sort_by')) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'latest':
                default:
                    $query->latest(); // Default sorting
                    break;
            }
        } else {
            $query->latest(); // Default sorting if no sort_by is provided
        }


        $products = $query->paginate(12)->withQueryString(); // Keep query string for pagination
        $categories = Category::all();

        if ($request->ajax()) {
            return view('products._product_list', compact('products'));
        }

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        // Eager load category and images for the specific product
        $product->load('category', 'images');

        // Eager load favoritedBy relationship for the authenticated user
        if (auth()->check()) {
            $product->loadExists(['favoritedBy as is_favorited_by_user' => function ($query) {
                $query->where('user_id', auth()->id());
            }]);
        }
        return view('products.show', compact('product'));
    }
}
