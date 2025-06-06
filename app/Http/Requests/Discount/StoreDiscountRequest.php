<?php

namespace App\Http\Requests\Discount;

use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use App\Enums\Product\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'type' => [
                'required',
                Rule::enum(DiscountType::class)
            ],
            'amount' => [
                'required_if:type,fixed',
                'numeric',
                'min:0'
            ],
            'rate' => [
                'required_if:type,percentage',
                'numeric',
                'between:0,100'
            ],
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id'
            ],
            'starts_at' => [
                'required',
                'date'
            ],
            'ends_at' => [
                'required',
                'date',
                'after:starts_at'
            ],
            'is_active' => ['boolean'],
            'usage_limit' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'per_user_limit' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'min_order_amount' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'min_items_quantity' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'scope' => [
                'required',
                Rule::enum(DiscountScope::class)
            ],

            'code' => [
                'nullable',
                'string',
                'max:32',
                'unique:discounts,code,NULL,id,deleted_at,NULL'
            ],
            'is_code_required' => ['boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'products' => [
                'nullable',
                'array'
            ],
            'products.*.product_id' => [
                'required',
                'integer',
            ],
            'products.*.product_type' => [
                'required',
                Rule::enum(ProductType::class)
            ],
            'products.*.price_id' => [
                'required',
                'integer',
                'exists:prices,id'
            ],
            'category_ids' => [
                'nullable',
                'array'
            ],
            'category_ids.*' => ['exists:categories,id'],
        ];
    }
}
