@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Manual Stock Adjustments') }}</h2>
        <div>
            <a href="{{ route('admin.stock-management.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-list me-1"></i> {{ __('Stock Log') }}
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.stock-management.process-adjustment') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="product_id" class="form-label">{{ __('Select Product') }} <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                <option value="">{{ __('Choose a product...') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} ({{ __('Current Stock') }}: {{ $product->stock_quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="adjustment_type" class="form-label">{{ __('Adjustment Type') }} <span class="text-danger">*</span></label>
                            <select name="adjustment_type" id="adjustment_type" class="form-select" required onchange="toggleReasonOptions()">
                                <option value="">{{ __('Select type...') }}</option>
                                <option value="increase">{{ __('Increase Stock') }}</option>
                                <option value="decrease">{{ __('Decrease Stock') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">{{ __('Reason') }} <span class="text-danger">*</span></label>
                            <select name="reason" id="reason" class="form-select" required>
                                <option value="">{{ __('Select a reason...') }}</option>
                                <option value="restock">{{ __('Restock') }}</option>
                                <option value="adjustment">{{ __('Adjustment') }}</option>
                                <option value="damaged">{{ __('Damaged Products') }}</option>
                                <option value="expired">{{ __('Expired Products') }}</option>
                                <option value="internal_use">{{ __('Internal Use') }}</option>
                                <option value="return">{{ __('Return from Customer') }}</option>
                                <option value="other">{{ __('Other') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Additional Details') }}</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="{{ __('Provide any additional details about this stock adjustment...') }}"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.stock-management.index') }}" class="btn btn-outline-secondary me-md-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Process Adjustment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleReasonOptions() {
        const adjustmentType = document.getElementById('adjustment_type');
        const reasonSelect = document.getElementById('reason');
        
        // Reset the reason options first
        reasonSelect.selectedIndex = 0;
        
        if (adjustmentType.value === 'increase') {
            // Show reasons relevant for increasing stock
            Array.from(reasonSelect.options).forEach(option => {
                option.hidden = false;
            });
            // Hide decrease-specific options if any
        } else if (adjustmentType.value === 'decrease') {
            // Show reasons relevant for decreasing stock
            Array.from(reasonSelect.options).forEach(option => {
                option.hidden = false;
            });
            // Hide increase-specific options if any
        }
    }
</script>
@endsection