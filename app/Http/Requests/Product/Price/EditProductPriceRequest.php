<?php

namespace App\Http\Requests\Product\Price;

use App\Enums\Price\PriceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditProductPriceRequest extends FormRequest
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
            'created_by_user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'price_type' => [
                'sometimes',
                'string',
                Rule::enum(PriceType::class),
            ],
            'amount' => ['sometimes', 'numeric'],
            'currency_id' => ['sometimes', 'integer', 'exists:currencies,id'],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'is_active' => ['sometimes', 'boolean'],
            'valid_from' => ['sometimes', 'date'],
            'valid_to' => ['sometimes', 'date', 'after:valid_from'],
            'tax_rate_ids' => ['sometimes', 'array'],
            'tax_rate_ids.*' => ['integer', 'exists:tax_rates,id'],
            'discount_ids' => ['sometimes', 'array'],
            'discount_ids.*' => ['integer', 'exists:discounts,id'],
        ];
    }
}
