@php
    $cart = null;
    if(auth()->check()) {
        $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
    } else {
        $cart = \App\Models\Cart::where('session_id', session()->getId())->first();
    }
    
    if(!$cart) {
        $cart = new \App\Models\Cart();
        $cart->cartItems = collect();
    }
@endphp

@if($cart->cartItems->count() > 0)
    <ul class="list-group mb-3">
        @foreach($cart->cartItems as $item)
            <li class="list-group-item d-flex justify-content-between lh-condensed">
                <div>
                    <h6 class="my-0">{{ $item->product->name ?? 'Product Name' }}</h6>
                    <small class="text-muted">Qty: {{ $item->quantity }}</small>
                </div>
                <span class="text-muted">${{ number_format($item->price * $item->quantity, 2) }}</span>
            </li>
        @endforeach
    </ul>

    <ul class="list-group mb-3">
        <li class="list-group-item d-flex justify-content-between">
            <span>Subtotal</span>
            <strong>${{ number_format($cart->subtotal, 2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Shipping</span>
            <strong>${{ number_format($cart->shipping_cost, 2) }}</strong>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Total (USD)</span>
            <strong>${{ number_format($cart->total, 2) }}</strong>
        </li>
    </ul>
@else
    <p class="text-muted">Your cart is empty</p>
@endif