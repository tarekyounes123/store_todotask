<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if there's enough stock
        if (!$product->hasStock($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available. Only ' . $product->stock_quantity . ' items in stock.'
            ], 400);
        }

        $cart = $this->getOrCreateCart();

        // Check if the product is already in the cart
        $cartItem = $cart->cartItems()->where('product_id', $request->product_id)->first();

        // Calculate total quantity (existing + new)
        $totalQuantity = $cartItem ? ($cartItem->quantity + $request->quantity) : $request->quantity;

        // Check if total quantity exceeds available stock
        if (!$product->hasStock($totalQuantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available. Only ' . $product->stock_quantity . ' items in stock. You currently have ' . ($cartItem ? $cartItem->quantity : 0) . ' in your cart.'
            ], 400);
        }

        if ($cartItem) {
            // Update the quantity if the product already exists in the cart
            $cartItem->quantity = $totalQuantity;
            $cartItem->save();
        } else {
            // Add new item to cart
            $cart->cartItems()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price, // Store the price at the time of adding
            ]);
        }

        $this->updateCartTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $this->getCartItemCount(),
            'cart_total' => $cart->fresh()->total
        ]);
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

        $product = $cartItem->product;

        // Check if there's enough stock for the requested quantity
        if (!$product->hasStock($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available. Only ' . $product->stock_quantity . ' items in stock.'
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

        return response()->json([
            'cart_count' => $this->getCartItemCount(),
            'subtotal' => $cart->subtotal,
            'shipping_cost' => $cart->shipping_cost,
            'total' => $cart->total
        ]);
    }
}