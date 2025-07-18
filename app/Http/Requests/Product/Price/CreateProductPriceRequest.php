<?php

namespace App\Http\Requests\Product\Price;

use App\Enums\Price\PriceType;
use App\Enums\Subscription\SubscriptionIntervalUnit;
use App\Enums\Subscription\SubscriptionTenureType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductPriceRequest extends FormRequest
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
                'required',
                'string',
                Rule::enum(PriceType::class),
            ],
            'amount' => ['required', 'numeric'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'is_active' => ['required', 'boolean'],
            'valid_from' => ['sometimes', 'date', 'nullable'],
            'valid_to' => ['sometimes', 'date', 'nullable', 'after:valid_from'],
            'tax_rate_ids' => ['sometimes', 'array'],
            'tax_rate_ids.*' => ['integer', 'exists:tax_rates,id'],
            'discount_ids' => ['sometimes', 'array'],
            'discount_ids.*' => ['integer', 'exists:discounts,id'],

            "label" => ['required_if:price_type,' . PriceType::SUBSCRIPTION->value, 'string', 'max:255'],
            "description" => ['required_if:price_type,' . PriceType::SUBSCRIPTION->value, 'string', 'max:1000'],
            "setup_fee" => ['required_if:price_type,' . PriceType::SUBSCRIPTION->value, 'array'],
            "setup_fee.value" => ['required', 'numeric'],
            "setup_fee.currency_id" => ['required', 'integer', 'exists:currencies,id'],
            "items" => ['required_if:price_type,' . PriceType::SUBSCRIPTION->value, 'array'],
            "items.*.frequency" => ['required', 'array'],
            "items.*.frequency.interval_unit" => [
                'required',
                'string',
                Rule::enum(SubscriptionIntervalUnit::class)
            ],
            "items.*.frequency.interval_count" => ['required', 'integer', 'min:1'],
            'items.*.tenure_type' => [
                'required',
                'string',
                Rule::enum(SubscriptionTenureType::class)
            ],
            'items.*.sequence' => ['required', 'integer', 'min:1'],
            'items.*.total_cycles' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'array'],
            'items.*.price.value' => ['required', 'numeric'],
            'items.*.price.currency_id' => ['required', 'integer', 'exists:currencies,id'],
        ];
    }
}
