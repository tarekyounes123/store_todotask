<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Products\ProductVariant; // Import ProductVariant
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the cart contents
     */
    public function index()
    {
        try {
            $cart = $this->getOrCreateCart();
            // Eager load product and variant details for display
            $cart->load('cartItems.product.images', 'cartItems.productVariant.terms.attribute');
            return view('cart.index', compact('cart'));
        } catch (\Exception $e) {
            \Log::error('Cart view error: ' . $e->getMessage());
            return view('cart.index', ['cart' => null]);
        }
    }

    /**
     * Add a product to the cart
     */
    public function addToCart(Request $request)
    {
        try {
            // Log the incoming request for debugging
            \Log::info('Add to cart request:', $request->all());

            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
                'product_variant_id' => 'nullable|exists:product_variants,id', // New: nullable for simple products
                'quantity' => 'required|integer|min:1|max:999'
            ]);

            $product = Product::findOrFail($request->product_id);
            $itemPrice = $product->price;
            $stockSource = $product; // Default to product for stock

            // If a variant is selected, use its details
            if ($request->filled('product_variant_id')) {
                \Log::info('Looking for variant with ID: ' . $request->product_variant_id . ' for product: ' . $request->product_id);

                $variant = ProductVariant::where('id', $request->product_variant_id)
                                        ->where('product_id', $request->product_id)  // Use request->product_id instead of $product->id
                                        ->first();

                if (!$variant) {
                    \Log::error('Variant not found for ID: ' . $request->product_variant_id . ' and product: ' . $request->product_id);
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected variant does not exist for this product.'
                    ], 400);
                }

                $itemPrice = $variant->price ?? $product->price; // Use variant price if available, else product price
                $stockSource = $variant; // Use variant for stock
                \Log::info('Using variant price: ' . $itemPrice . ' and stock: ' . $stockSource->stock_quantity);
            }

            // Check if there's enough stock
            if (!$stockSource->hasStock($request->quantity)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available. Only ' . $stockSource->stock_quantity . ' items in stock.'
                ], 400);
            }

            $cart = $this->getOrCreateCart();

            // Check if the product/variant combination is already in the cart
            $cartItemQuery = $cart->cartItems()->where('product_id', $request->product_id);
            if ($request->filled('product_variant_id')) {
                $cartItemQuery->where('product_variant_id', $request->product_variant_id);
            } else {
                $cartItemQuery->whereNull('product_variant_id');
            }
            $cartItem = $cartItemQuery->first();

            // Calculate total quantity (existing + new)
            $totalQuantity = $cartItem ? ($cartItem->quantity + $request->quantity) : $request->quantity;

            // Check if total quantity exceeds available stock
            if (!$stockSource->hasStock($totalQuantity)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available. Only ' . $stockSource->stock_quantity . ' items in stock. You currently have ' . ($cartItem ? $cartItem->quantity : 0) . ' in your cart.'
                ], 400);
            }

            if ($cartItem) {
                // Update the quantity if the product/variant already exists in the cart
                $cartItem->quantity = $totalQuantity;
                $cartItem->save();
            } else {
                // Add new item to cart - create with error handling
                // Verify the variant exists before attempting to save
                if ($request->filled('product_variant_id')) {
                    $variantCheck = ProductVariant::find($request->product_variant_id);
                    if (!$variantCheck) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected product variant does not exist.'
                        ], 400);
                    }
                    // Also verify the variant belongs to the product
                    if ($variantCheck->product_id != $request->product_id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected product variant does not belong to this product.'
                        ], 400);
                    }
                }

                $cart->cartItems()->create([
                    'product_id' => $request->product_id,
                    'product_variant_id' => $request->product_variant_id,
                    'quantity' => $request->quantity,
                    'price' => $itemPrice,
                ]);
            }

            $this->updateCartTotal($cart);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => $this->getCartItemCount(),
                'cart_total' => $cart->fresh()->total
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error adding to cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided: ' . collect($e->errors())->flatten()->first()
            ], 400);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error adding to cart: ' . $e->getMessage());
            \Log::error('SQL: ' . $e->getSql());
            \Log::error('Bindings: ' . json_encode($e->getBindings()));
            return response()->json([
                'success' => false,
                'message' => 'A database error occurred while adding the product to cart. Please try again.'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Error adding to cart: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' at line ' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the product to cart. Please try again.'
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        $cartItem = CartItem::findOrFail($request->cart_item_id);

        // Check if the user owns this cart item
        if ($cartItem->cart->user_id !== Auth::id() && $cartItem->cart->session_id !== session()->getId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Determine stock source (product or variant)
        $stockSource = $cartItem->productVariant ?? $cartItem->product;

        // Check if there's enough stock for the requested quantity
        if (!$stockSource->hasStock($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available. Only ' . $stockSource->stock_quantity . ' items in stock.'
            ], 400);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $cart = $cartItem->cart;
        $this->updateCartTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_count' => $this->getCartItemCount(),
            'cart_total' => $cart->fresh()->total,
            'item_subtotal' => $cartItem->price * $cartItem->quantity
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($id)
    {
        $cartItem = CartItem::findOrFail($id);

        // Check if the user owns this cart item
        if ($cartItem->cart->user_id !== Auth::id() && $cartItem->cart->session_id !== session()->getId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        $this->updateCartTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $this->getCartItemCount(),
            'cart_total' => $cart->fresh()->total ?? 0
        ]);
    }

    /**
     * Clear the entire cart
     */
    public function clearCart()
    {
        $cart = $this->getOrCreateCart();

        $cart->cartItems()->delete();
        $cart->total = 0;
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0,
            'cart_total' => 0
        ]);
    }

    /**
     * Calculate and update the cart total including shipping
     */
    public function updateCartTotal($cart)
    {
        // Calculate the total manually to store it in the database
        $subtotal = 0;
        foreach ($cart->cartItems as $item) {
            $subtotal += $item->price * $item->quantity;
        }

        $shippingCost = $subtotal < 50 && $subtotal > 0 ? 5.00 : 0.00;
        $total = $subtotal + $shippingCost;

        $cart->total = $total;
        $cart->save();

        return $cart;
    }

    /**
     * Get the cart for the current user or session
     */
    private function getOrCreateCart()
    {
        if (Auth::check()) {
            // Check if the user already has a cart
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['user_id' => Auth::id(), 'total' => 0]
            );
        } else {
            // Create or get a session-based cart
            $sessionId = session()->getId();

            $cart = Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['session_id' => $sessionId, 'total' => 0]
            );
        }

        return $cart;
    }

    /**
     * Get the total number of items in the cart
     */
    private function getCartItemCount()
    {
        $cart = $this->getOrCreateCart();
        return $cart->cartItems->sum('quantity');
    }

    /**
     * Get cart summary with subtotal, shipping, and total
     */
    public function getCartSummary()
    {
        $cart = $this->getOrCreateCart();

        // Refresh the cart data to ensure we have the latest values
        $cart = $cart->fresh();

        return response()->json([
            'cart_count' => $this->getCartItemCount(),
            'subtotal' => $cart->subtotal,
            'shipping_cost' => $cart->shipping_cost,
            'total' => $cart->total
        ]);
    }
}
