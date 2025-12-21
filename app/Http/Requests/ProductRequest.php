<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0', // Added this rule
            'stock_quantity' => 'required|integer|min:0', // Added this rule
            'category_id' => 'required|exists:categories,id',
            'attributes' => 'array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.terms' => 'required|array',
            'attributes.*.is_variant_attribute' => 'boolean',
            'variants' => 'array',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('product_variants')->ignore($this->variant_id),
            ],
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.terms' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $product = $this->route('product');
                    if (!$product) {
                        return;
                    }
                    $variants = $product->variants()->with('terms')->get();
                    $termIds = collect($value)->sort()->values()->all();

                    foreach ($variants as $variant) {
                        if ($variant->terms->pluck('id')->sort()->values()->all() === $termIds) {
                            $fail('The variant with the same attribute combination already exists.');
                        }
                    }
                }
            ]
        ];
    }
}
