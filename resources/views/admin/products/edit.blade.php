@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-0">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {{ __('Edit Product') }}
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">{{ __('Back to Products') }}</a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" id="description" rows="5" class="form-control" required>{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('Selling Price') }}</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="buy_price" class="form-label">{{ __('Buy Price / Unit Cost') }}</label>
                            <input type="number" name="buy_price" id="buy_price" value="{{ old('buy_price', $product->buy_price ?? 0) }}" step="0.01" class="form-control" min="0" required>
                            <div class="form-text">{{ __('Cost of the product when you purchase it') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label">{{ __('Stock Quantity') }}</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" class="form-control" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">{{ __('Category') }}</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">{{ __('Select a Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Product Attributes Section --}}
                        <div class="card mt-4">
                            <div class="card-header">Product Attributes</div>
                            <div class="card-body">
                                <div id="product-attributes-container">
                                    {{-- Existing product attributes will be rendered here --}}
                                    @foreach ($product->attributes as $prodAttr)
                                        <div class="mb-3 p-3 border rounded product-attribute-item" data-attribute-id="{{ $prodAttr->attribute_id ?? 'custom-' . $prodAttr->id }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">{{ $prodAttr->name }}</h5>
                                                <button type="button" class="btn btn-danger btn-sm remove-product-attribute">Remove</button>
                                            </div>
                                            <input type="hidden" name="attributes[{{ $prodAttr->attribute_id }}][attribute_id]" value="{{ $prodAttr->attribute_id }}">
                                            <input type="hidden" name="attributes[{{ $prodAttr->attribute_id }}][name]" value="{{ $prodAttr->name }}">

                                            <div class="form-check">
                                                <input class="form-check-input is-variant-attribute-checkbox" type="checkbox"
                                                       name="attributes[{{ $prodAttr->attribute_id }}][is_variant_attribute]"
                                                       id="is_variant_attribute_{{ $prodAttr->attribute_id }}"
                                                       value="1" {{ $prodAttr->is_variant_attribute ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_variant_attribute_{{ $prodAttr->attribute_id }}">
                                                    Use for variations
                                                </label>
                                            </div>
                                            <div class="attribute-terms-wrapper mt-2">
                                                <h6>Terms:</h6>
                                                <div class="d-flex flex-wrap align-items-center mb-2">
                                                    @foreach ($prodAttr->terms as $prodAttrTerm)
                                                        <span class="badge bg-primary me-1 term-badge"
                                                            data-term-value="{{ $prodAttrTerm->attributeTerm->value ?? $prodAttrTerm->value }}">
                                                            {{ $prodAttrTerm->attributeTerm->value ?? $prodAttrTerm->value }}
                                                            <button type="button" class="btn-close btn-close-white remove-term-btn" aria-label="Close"></button>
                                                            <input type="hidden" name="attributes[{{ $prodAttr->attribute_id }}][terms][{{ $loop->index }}][id]" value="{{ $prodAttrTerm->attribute_term_id }}">
                                                            <input type="hidden" name="attributes[{{ $prodAttr->attribute_id }}][terms][{{ $loop->index }}][value]" value="{{ $prodAttrTerm->attributeTerm->value ?? $prodAttrTerm->value }}">
                                                        </span>
                                                    @endforeach
                                                    <input type="text" class="form-control form-control-sm w-auto d-inline-block add-term-input" placeholder="New term">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary add-term-btn">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mb-3 mt-3">
                                    <label for="add-attribute-select" class="form-label">Add Global Attribute:</label>
                                    <select id="add-attribute-select" class="form-select">
                                        <option value="">-- Select an Attribute --</option>
                                        @foreach ($attributes as $attr)
                                            <option value="{{ $attr->id }}" data-attribute-name="{{ $attr->name }}">
                                                {{ $attr->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="add-selected-attribute-btn" class="btn btn-success mt-2">Add Selected Attribute</button>
                                </div>
                            </div>
                        </div>

                        {{-- Product Variants Section --}}
                        <div class="card mt-4">
                            <div class="card-header">Product Variants</div>
                            <div class="card-body">
                                <button type="button" id="generate-variants-btn" class="btn btn-primary mb-3">Generate Variants</button>

                                <div id="product-variants-container">
                                    @if ($product->variants->isNotEmpty())
                                        <table class="table table-bordered" id="variants-table">
                                            <thead>
                                                <tr>
                                                    <th>Variant</th>
                                                    <th>SKU</th>
                                                    <th>Price</th>
                                                    <th>Stock</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($product->variants as $variant)
                                                    <tr data-variant-id="{{ $variant->id }}">
                                                        <td>
                                                            @php
                                                                $variantTerms = $variant->terms->map(function($term) {
                                                                    return $term->attribute->name . ': ' . $term->value;
                                                                })->implode(', ');
                                                            @endphp
                                                            {{ $variantTerms }}
                                                            @foreach ($variant->terms as $term)
                                                                <input type="hidden" name="variants[{{ $variant->id }}][terms][]" value="{{ $term->id }}">
                                                            @endforeach
                                                            <input type="hidden" name="variants[{{ $variant->id }}][id]" value="{{ $variant->id }}">
                                                        </td>
                                                        <td><input type="text" name="variants[{{ $variant->id }}][sku]" value="{{ old('variants.' . $variant->id . '.sku', $variant->sku) }}" class="form-control"></td>
                                                        <td><input type="number" name="variants[{{ $variant->id }}][price]" value="{{ old('variants.' . $variant->id . '.price', $variant->price) }}" step="0.01" class="form-control"></td>
                                                        <td><input type="number" name="variants[{{ $variant->id }}][stock_quantity]" value="{{ old('variants.' . $variant->id . '.stock_quantity', $variant->stock_quantity) }}" class="form-control"></td>
                                                        <td>
                                                            <select name="variants[{{ $variant->id }}][is_enabled]" class="form-select">
                                                                <option value="1" {{ old('variants.' . $variant->id . '.is_enabled', $variant->is_enabled) ? 'selected' : '' }}>Enabled</option>
                                                                <option value="0" {{ !old('variants.' . $variant->id . '.is_enabled', $variant->is_enabled) ? 'selected' : '' }}>Disabled</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-variant-btn">Remove</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p id="no-variants-message">No variants generated yet.</p>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="mb-3">
                            <label for="images" class="form-label">{{ __('New Product Images') }}</label>
                            <input type="file" name="images[]" id="images" multiple class="form-control">
                            <div class="mt-2">
                                <label class="form-label">{{ __('Existing Images') }}</label>
                                <div class="d-flex flex-wrap">
                                    @foreach ($product->images as $image)
                                        <div class="me-2 mb-2 border p-1 rounded">
                                            <img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $image->id }}" id="delete_image_{{ $image->id }}">
                                                <label class="form-check-label" for="delete_image_{{ $image->id }}">
                                                    {{ __('Delete') }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Update Product') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productAttributesContainer = document.getElementById('product-attributes-container');
        const addAttributeSelect = document.getElementById('add-attribute-select');
        const addSelectedAttributeBtn = document.getElementById('add-selected-attribute-btn');
        const generateVariantsBtn = document.getElementById('generate-variants-btn');
        const productVariantsContainer = document.getElementById('product-variants-container');
        let attributeIdCounter = {{ $attributes->max('id') ? $attributes->max('id') + 1 : 1 }};

        // Function to generate unique IDs for new elements
        function getUniqueId() {
            return 'new-' + attributeIdCounter++;
        }

        // --- Product Attribute Management ---
        addSelectedAttributeBtn.addEventListener('click', function () {
            const selectedOption = addAttributeSelect.options[addAttributeSelect.selectedIndex];
            const attributeId = selectedOption.value;
            const attributeName = selectedOption.dataset.attributeName;

            if (!attributeId) {
                alert('Please select an attribute.');
                return;
            }

            // Check if attribute is already added
            if (document.querySelector(`.product-attribute-item[data-attribute-id="${attributeId}"]`)) {
                alert(`Attribute "${attributeName}" is already added.`);
                return;
            }

            const uniqueId = attributeId; // Use actual attribute ID for global attributes

            const attributeHtml = `
                <div class="mb-3 p-3 border rounded product-attribute-item" data-attribute-id="${uniqueId}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">${attributeName}</h5>
                        <button type="button" class="btn btn-danger btn-sm remove-product-attribute">Remove</button>
                    </div>
                    <input type="hidden" name="attributes[${uniqueId}][attribute_id]" value="${attributeId}">
                    <input type="hidden" name="attributes[${uniqueId}][name]" value="${attributeName}">
                    <div class="form-check">
                        <input class="form-check-input is-variant-attribute-checkbox" type="checkbox"
                               name="attributes[${uniqueId}][is_variant_attribute]"
                               id="is_variant_attribute_${uniqueId}" value="1">
                        <label class="form-check-label" for="is_variant_attribute_${uniqueId}">
                            Use for variations
                        </label>
                    </div>
                    <div class="attribute-terms-wrapper mt-2">
                        <h6>Terms:</h6>
                        <div class="d-flex flex-wrap align-items-center mb-2">
                            <input type="text" class="form-control form-control-sm w-auto d-inline-block add-term-input" placeholder="New term">
                            <button type="button" class="btn btn-sm btn-outline-secondary add-term-btn">Add</button>
                        </div>
                    </div>
                </div>
            `;
            productAttributesContainer.insertAdjacentHTML('beforeend', attributeHtml);
            addAttributeSelect.selectedIndex = 0; // Reset select
        });

        // Remove Product Attribute
        productAttributesContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-product-attribute')) {
                e.target.closest('.product-attribute-item').remove();
            }
        });

        // Add Term for Product Attribute
        productAttributesContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-term-btn')) {
                const termInput = e.target.previousElementSibling;
                const termValue = termInput.value.trim();
                if (termValue) {
                    const attributeItem = e.target.closest('.product-attribute-item');
                    const attributeUniqueId = attributeItem.dataset.attributeId;
                    const termsWrapper = attributeItem.querySelector('.attribute-terms-wrapper > div');

                    // Check for duplicate term within this attribute
                    const existingTerms = Array.from(termsWrapper.querySelectorAll('.term-badge'))
                                            .map(badge => badge.dataset.termValue);
                    if (existingTerms.includes(termValue)) {
                        alert(`Term "${termValue}" already exists for this attribute.`);
                        return;
                    }

                    const termHtml = `
                        <span class="badge bg-primary me-1 term-badge" data-term-value="${termValue}">
                            ${termValue}
                            <button type="button" class="btn-close btn-close-white remove-term-btn" aria-label="Close"></button>
                            <input type="hidden" name="attributes[${attributeUniqueId}][terms][][id]" value="">
                            <input type="hidden" name="attributes[${attributeUniqueId}][terms][][value]" value="${termValue}">
                        </span>
                    `;
                    termsWrapper.insertAdjacentHTML('afterbegin', termHtml);
                    termInput.value = '';
                }
            }
        });

        // Remove Term from Product Attribute
        productAttributesContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-term-btn')) {
                e.target.closest('.term-badge').remove();
            }
        });


        // --- Variant Generation ---
        generateVariantsBtn.addEventListener('click', function () {
            const variantAttributes = [];
            const existingVariants = {};

            // Collect existing variants to preserve their data
            productVariantsContainer.querySelectorAll('tr[data-variant-id]').forEach(row => {
                const variantId = row.dataset.variantId;
                const termsInputs = Array.from(row.querySelectorAll('input[name^="variants"][name$="[terms][]"]'));
                const terms = termsInputs.map(input => input.value).sort();
                
                const variantKey = terms.join('-'); // Simple key for combination
                existingVariants[variantKey] = {
                    id: variantId,
                    sku: row.querySelector(`input[name="variants[${variantId}][sku]"]`).value,
                    price: row.querySelector(`input[name="variants[${variantId}][price]"]`).value,
                    stock_quantity: row.querySelector(`input[name="variants[${variantId}][stock_quantity]"]`).value,
                    is_enabled: row.querySelector(`select[name="variants[${variantId}][is_enabled]"]`).value,
                };
            });


            productAttributesContainer.querySelectorAll('.product-attribute-item').forEach(item => {
                const isVariantAttributeCheckbox = item.querySelector('.is-variant-attribute-checkbox');
                if (isVariantAttributeCheckbox && isVariantAttributeCheckbox.checked) {
                    const attributeId = item.dataset.attributeId;
                    const attributeName = item.querySelector('h5').textContent.trim();
                    const terms = [];
                    item.querySelectorAll('.attribute-terms-wrapper .term-badge').forEach(badge => {
                        // Find the hidden input for the term ID within the badge
                        const hiddenIdInput = badge.querySelector('input[type="hidden"][name*="[id]"]');
                        const termId = hiddenIdInput ? hiddenIdInput.value : null;

                        terms.push({
                            value: badge.dataset.termValue,
                            id: termId, // Use the ID from the hidden input
                            attributeName: attributeName, // Store attribute name for display
                            attributeId: attributeId, // Store attribute ID for hidden input
                        });
                    });
                    if (terms.length > 0) {
                        variantAttributes.push(terms);
                    }
                }
            });

            if (variantAttributes.length === 0) {
                alert('Please select at least one attribute and mark it "Use for variations" with some terms.');
                return;
            }

            const combinations = generateCombinations(variantAttributes);
            renderVariants(combinations, existingVariants);
        });

        function generateCombinations(arrays) {
            let result = [[]];
            for (let i = 0; i < arrays.length; i++) {
                let currentAttributeTerms = arrays[i];
                let temp = [];
                for (let j = 0; j < result.length; j++) {
                    for (let k = 0; k < currentAttributeTerms.length; k++) {
                        temp.push(result[j].concat(currentAttributeTerms[k]));
                    }
                }
                result = temp;
            }
            return result;
        }

        function renderVariants(combinations, existingVariants) {
            let variantsTableHtml = `
                <table class="table table-bordered" id="variants-table">
                    <thead>
                        <tr>
                            <th>Variant</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            if (combinations.length > 0) {
                combinations.forEach((combination, index) => {
                    const termsDisplay = combination.map(term => `${term.attributeName}: ${term.value}`).join(', ');
                    const termIds = combination.map(term => term.attributeId);
                    const termValues = combination.map(term => term.value).sort();
                    const variantKey = termValues.join('-');

                    // Try to pre-fill data from existing variants
                    const prefillData = existingVariants[variantKey] || {
                        id: 'new-' + index, // Use a temporary ID for new variants
                        sku: '',
                        price: document.getElementById('price').value, // Default to product price
                        stock_quantity: document.getElementById('stock_quantity').value, // Default to product stock
                        is_enabled: '1',
                    };
                    const variantId = prefillData.id;

                    variantsTableHtml += `
                        <tr data-variant-id="${variantId}">
                            <td>
                                ${termsDisplay}
                                ${combination.map((term, termIndex) => `
                                    <input type="hidden" name="variants[${variantId}][terms][${termIndex}][attribute_term_id]" value="${term.id}">
                                    <input type="hidden" name="variants[${variantId}][terms][${termIndex}][value]" value="${term.value}">
                                    <input type="hidden" name="variants[${variantId}][terms][${termIndex}][attribute_id]" value="${term.attributeId}">
                                `).join('')}
                                ${!variantId.startsWith('new-') ? `<input type="hidden" name="variants[${variantId}][id]" value="${variantId}">` : ''}
                            </td>
                            <td><input type="text" name="variants[${variantId}][sku]" value="${prefillData.sku}" class="form-control"></td>
                            <td><input type="number" name="variants[${variantId}][price]" value="${prefillData.price}" step="0.01" class="form-control"></td>
                            <td><input type="number" name="variants[${variantId}][stock_quantity]" value="${prefillData.stock_quantity}" class="form-control"></td>
                            <td>
                                <select name="variants[${variantId}][is_enabled]" class="form-select">
                                    <option value="1" ${prefillData.is_enabled == '1' ? 'selected' : ''}>Enabled</option>
                                    <option value="0" ${prefillData.is_enabled == '0' ? 'selected' : ''}>Disabled</option>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-variant-btn">Remove</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                variantsTableHtml += `<tr><td colspan="6">No variants generated.</td></tr>`;
            }
            
            variantsTableHtml += `</tbody></table>`;
            productVariantsContainer.innerHTML = variantsTableHtml;
            document.getElementById('no-variants-message')?.remove();
        }

        // Remove Variant
        productVariantsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-variant-btn')) {
                e.target.closest('tr').remove();
                if (productVariantsContainer.querySelectorAll('tr[data-variant-id]').length === 0) {
                    productVariantsContainer.innerHTML = '<p id="no-variants-message">No variants generated yet.</p>';
                }
            }
        });
    });
</script>
@endpush
