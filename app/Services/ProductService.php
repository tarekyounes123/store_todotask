<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get products with filters, sorting, and pagination
     */
    public function getProducts(Request $request, int $perPage = 12)
    {
        return $this->productRepository->getProducts($request, $perPage);
    }

    /**
     * Get all categories for filtering
     */
    public function getCategories()
    {
        return $this->productRepository->getCategories();
    }

    /**
     * Get a product with optimized relationships
     */
    public function getProductWithRelationships($product)
    {
        return $this->productRepository->getProductWithRelationships($product);
    }
}