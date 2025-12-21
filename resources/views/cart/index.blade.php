@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Shopping Cart') }}</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="mb-4">{{ __('Shopping Cart') }}</h2>

            @if(isset($cart) && $cart->cartItems && $cart->cartItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">{{ __('Product') }}</th>
                                <th scope="col">{{ __('Price') }}</th>
                                <th scope="col">{{ __('Quantity') }}</th>
                                <th scope="col">{{ __('Subtotal') }}</th>
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart->cartItems as $item)
                                <tr data-item-id="{{ $item->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $imagePath = 'https://via.placeholder.com/50x50.png?text=No+Image';
                                                if ($item->productVariant && $item->productVariant->image_path) {
                                                    $imagePath = Storage::url($item->productVariant->image_path);
                                                } elseif ($item->product->images->isNotEmpty()) {
                                                    $imagePath = Storage::url($item->product->images->first()->image_path);
                                                }
                                            @endphp
                                            <img src="{{ $imagePath }}" alt="{{ $item->product->name ?? 'Product' }}" class="img-thumbnail me-3" style="width: 70px; height: 70px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $item->product->name ?? 'Product Name' }}</h6>
                                                @if ($item->productVariant)
                                                    <small class="text-muted">
                                                        @php
                                                            $variantDetails = $item->productVariant->terms->map(function($term) {
                                                                return $term->attribute->name . ': ' . $term->value;
                                                            })->implode(', ');
                                                        @endphp
                                                        {{ $variantDetails }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <button
                                                class="btn btn-outline-secondary btn-sm"
                                                onclick="updateQuantityByOne({{ $item->id }}, -1)"
                                                @if($item->quantity <= 1) disabled @endif
                                            >
                                                -
                                            </button>
                                            <input
                                                type="number"
                                                id="qty-{{ $item->id }}"
                                                value="{{ $item->quantity }}"
                                                min="1"
                                                max="999"
                                                class="form-control form-control-sm mx-1 text-center"
                                                style="width: 70px;"
                                                onchange="updateQuantityDirect({{ $item->id }}, this.value)"
                                            />
                                            <button
                                                class="btn btn-outline-secondary btn-sm"
                                                onclick="updateQuantityByOne({{ $item->id }}, 1)"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td class="item-subtotal">
                                        ${{ number_format($item->price * $item->quantity, 2) }}
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="removeFromCart({{ $item->id }})"
                                        >
                                            {{ __('Remove') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <div class="row">
                        <div class="col-md-6">
                            <button
                                onclick="clearCart()"
                                class="btn btn-outline-danger"
                            >
                                {{ __('Clear Cart') }}
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <strong>{{ __('Subtotal:') }}</strong>
                                    <span id="cart-subtotal">${{ number_format($cart->subtotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <strong>{{ __('Shipping:') }}</strong>
                                    <span id="cart-shipping">${{ number_format($cart->shipping_cost, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold fs-5 pt-2">
                                    <strong>{{ __('Total:') }}</strong>
                                    <span id="cart-total">${{ number_format($cart->total, 2) }}</span>
                                </div>
                                <a href="{{ route('checkout.index') }}"
                                   class="btn btn-success btn-lg mt-3">
                                    {{ __('Proceed to Checkout') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">{{ __('Your cart is currently empty') }}</h4>
                    <p class="text-muted">{{ __('Add some products to your cart to see them here') }}</p>

                    <div class="mt-4">
                        <a href="{{ route('products.index') }}"
                           class="btn btn-primary btn-lg">
                            {{ __('Browse Products') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Update quantity by one (for + and - buttons)
    async function updateQuantityByOne(cartItemId, change) {
        // Get current quantity from the input field
        const qtyInput = document.getElementById(`qty-${cartItemId}`);
        if (!qtyInput) {
            console.error(`Quantity input not found for item ${cartItemId}`);
            return;
        }

        let currentQty = parseInt(qtyInput.value);
        let newQuantity = currentQty + change;

        await updateQuantity(cartItemId, newQuantity);
    }

    // Update quantity directly from input field
    async function updateQuantityDirect(cartItemId, newQuantity) {
        await updateQuantity(cartItemId, parseInt(newQuantity));
    }

    // Main update quantity function
    async function updateQuantity(cartItemId, newQuantity) {
        if (newQuantity < 1) {
            removeFromCart(cartItemId);
            return;
        }

        try {
            const response = await fetch(`/cart/update-quantity`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    cart_item_id: cartItemId,
                    quantity: newQuantity
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update the quantity in the input field
                const qtyInput = document.getElementById(`qty-${cartItemId}`);
                if (qtyInput) {
                    qtyInput.value = newQuantity;
                }

                // Update the specific item's subtotal display
                const itemRow = document.querySelector(`[data-item-id="${cartItemId}"]`);
                if(itemRow) {
                    const itemSubtotalElement = itemRow.querySelector('.item-subtotal');
                    if(itemSubtotalElement && data.item_subtotal !== undefined) {
                        itemSubtotalElement.textContent = '$' + data.item_subtotal.toFixed(2);
                    }
                }

                // Check if we need to update the minus button disabled state
                const minusButton = document.querySelector(`[data-item-id="${cartItemId}"] button[onclick*="${cartItemId}"][onclick*="-1"]`);
                if (minusButton) {
                    if (newQuantity <= 1) {
                        minusButton.disabled = true;
                    } else {
                        minusButton.disabled = false;
                    }
                }

                // Update overall cart summary
                updateCartSummary();
            } else {
                alert('Error updating quantity: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while updating quantity');
        }
    }

    // Remove item from cart
    async function removeFromCart(cartItemId) {
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }

        try {
            const response = await fetch(`/cart/remove/${cartItemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                // Remove the row from the table
                const itemRow = document.querySelector(`[data-item-id="${cartItemId}"]`);
                if (itemRow) {
                    itemRow.remove();
                }
                // Update cart summary
                updateCartSummary();
            } else {
                alert('Error removing item: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while removing item');
        }
    }

    // Clear entire cart
    async function clearCart() {
        if (!confirm('Are you sure you want to clear your entire cart?')) {
            return;
        }

        try {
            const response = await fetch('/cart/clear', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                // Remove all cart item rows
                const cartTableBody = document.querySelector('tbody');
                if (cartTableBody) {
                    cartTableBody.innerHTML = '';
                }
                // Update cart summary
                updateCartSummary();
            } else {
                alert('Error clearing cart: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while clearing cart');
        }
    }

    // Update cart display with new count and total
    function updateCartDisplay(data) {
        // Update cart count in header (if element exists)
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = data.cart_count || 0;
        }

        // Update cart totals display with proper fallbacks
        const subtotal = parseFloat(data.subtotal || 0).toFixed(2);
        const shipping = parseFloat(data.shipping_cost || 0).toFixed(2);
        const total = parseFloat(data.total || data.cart_total || 0).toFixed(2);

        // Update subtotal
        document.querySelectorAll('#cart-subtotal').forEach(el => {
            el.textContent = '$' + subtotal;
        });

        // Update shipping
        document.querySelectorAll('#cart-shipping').forEach(el => {
            el.textContent = '$' + shipping;
        });

        // Update total
        document.querySelectorAll('#cart-total').forEach(el => {
            el.textContent = '$' + total;
        });
    }

    // Get updated cart summary from server
    async function updateCartSummary() {
        try {
            const response = await fetch('/cart/summary', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data) {
                updateCartDisplay(data);
            }
        } catch (error) {
            console.error('Error fetching cart summary:', error);
        }
    }
</script>
@endsection
