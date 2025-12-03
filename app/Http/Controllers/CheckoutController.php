<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page with user information
     */
    public function index()
    {
        $user = Auth::user();

        // Split the name into first and last name if they're not already set
        if (empty($user->first_name) || empty($user->last_name)) {
            $nameParts = explode(' ', $user->name ?? '', 2);
            $user->first_name = $user->first_name ?? ($nameParts[0] ?? '');
            $user->last_name = $user->last_name ?? ($nameParts[1] ?? '');
        }

        return view('checkout.index', compact('user'));
    }

    /**
     * Process the checkout
     */
    public function process(Request $request)
    {
        // Validate the request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Update user information if it has changed
            $user = Auth::user();
            $user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
            ]);

            // Get the user's cart
            $cart = $user->cart;
            if (!$cart || $cart->cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
            }

            // Calculate totals
            $subtotal = $cart->subtotal;
            $shippingCost = $cart->shipping_cost;
            $total = $cart->total;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(dechex(time())) . '-' . rand(1000, 9999),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending',
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'special_instructions' => $request->special_instructions,
            ]);

            // Create order items from cart items and reduce stock
            foreach ($cart->cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                ]);

                // Reduce stock for the product
                $product = $cartItem->product;
                $product->reduceStock($cartItem->quantity, 'order', $order->id, "Stock reduced for order #{$order->order_number}");
            }

            // Dispatch event for the new order
            \App\Events\OrderCreated::dispatch($order);

            // Clear the cart after creating the order
            $cart->cartItems()->delete();
            $cart->total = 0;
            $cart->save();

            DB::commit();

            return redirect()->route('checkout.success')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('checkout.index')->with('error', 'There was an error processing your order: ' . $e->getMessage());
        }
    }
    
    /**
     * Display checkout success page
     */
    public function success()
    {
        return view('checkout.success');
    }
}