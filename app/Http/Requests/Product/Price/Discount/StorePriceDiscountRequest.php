<?php

namespace App\Http\Requests\Product\Price\Discount;

use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePriceDiscountRequest extends FormRequest
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
                'unique:discounts,code'
            ],
            'is_code_required' => ['boolean'],
            'is_default' => ['sometimes', 'boolean']
        ];
    }
}
