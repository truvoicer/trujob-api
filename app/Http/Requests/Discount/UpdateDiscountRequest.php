<?php

namespace App\Http\Requests\Discount;

use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDiscountRequest extends FormRequest
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
                'required',
                'numeric',
                'min:0'
            ],
            'currency_code' => [
                'nullable',
                'string',
                'size:3'
            ],
            'start_date' => [
                'required',
                'date'
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date'
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
                'unique:discounts,code'
            ],
            'is_code_required' => ['boolean'],
            'products' => [
                'nullable',
                'array'
            ],
            'products.*.productable_id' => [
                'integer',
            ],
            'products.*.productable_type' => [
                'string',
            ],
            'category_ids' => [
                'nullable',
                'array'
            ],
            'category_ids.*' => ['exists:categories,id'],
        ];
    
    }
    
}
