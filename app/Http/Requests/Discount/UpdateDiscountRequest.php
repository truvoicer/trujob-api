<?php

namespace App\Http\Requests\Discount;

use App\Enums\Order\Discount\DiscountableType;
use App\Enums\Order\Discount\DiscountAmountType;
use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use App\Enums\Product\ProductType;
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
                'sometimes',
                'string',
                'max:100'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'amount_type' => [
                'sometimes',
                Rule::enum(DiscountAmountType::class)
            ],
            'type' => [
                'sometimes',
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
                'sometimes',
                'integer',
                'exists:currencies,id'
            ],
            'starts_at' => [
                'sometimes',
                'date'
            ],
            'ends_at' => [
                'sometimes',
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
                'sometimes',
                Rule::enum(DiscountScope::class)
            ],
            'code' => [
                'nullable',
                'string',
                'max:32',
                'unique:discounts,code'
            ],
            'is_code_required' => ['boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'discountables' => [
                'sometimes',
                'array'
            ],
            'discountables.*.id' => [
                'required',
                'integer',
            ],
            'discountables.*.type' => [
                'required',
                Rule::enum(DiscountableType::class)
            ],
        ];

    }

}
