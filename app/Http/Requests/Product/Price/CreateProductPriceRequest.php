<?php

namespace App\Http\Requests\Product\Price;

use App\Enums\Price\PriceType;
use App\Enums\Subscription\SubscriptionIntervalUnit;
use App\Enums\Subscription\SubscriptionSetupFeeFailureAction;
use App\Enums\Subscription\SubscriptionTenureType;
use App\Enums\Subscription\SubscriptionType;
use App\Rules\GreaterThanPreviousSequence;
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
        $rules = [
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
            'type' => [
                'required_if:price_type,' . PriceType::SUBSCRIPTION->value,
                'string',
                Rule::enum(SubscriptionType::class)
            ],
            'start_time' => [
                'sometimes',
                'date',
                'nullable'
            ],
            'has_setup_fee' => [
                'sometimes',
                'boolean'
            ],
            "setup_fee" => [
                'sometimes',
                'array'
            ],
            "setup_fee_value" => [
                'required',
                'numeric'
            ],
            "setup_fee_currency_id" => [
                'required',
                'integer',
                'exists:currencies,id'
            ],
            'auto_bill_outstanding' => [
                'required_if:price_type,' . PriceType::SUBSCRIPTION->value,
                'boolean'
            ],
            'setup_fee_failure_action' => [
                'required_if:price_type,' . PriceType::SUBSCRIPTION->value,
                'string',
                Rule::enum(SubscriptionSetupFeeFailureAction::class)
            ],
            'payment_failure_threshold' => [
                'required_if:price_type,' . PriceType::SUBSCRIPTION->value,
                'integer',
                'min:0',
                'max:999'
            ],
            "items" => [
                'required_if:price_type,' . PriceType::SUBSCRIPTION->value,
                'array'
            ],
            "items.*.frequency" => [
                'required',
                'array'
            ],
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
            'items.*.sequence' => [
                'required',
                'integer',
                'min:1'
            ],
            'items.*.total_cycles' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'array'],
            'items.*.price.value' => ['required', 'numeric'],
            'items.*.price.currency_id' => ['required', 'integer', 'exists:currencies,id'],
        ];


        // Apply the custom rule to each 'sequence' field in the array
        if (request()->has('items') && is_array(request()->input('items'))) {
            foreach (request()->input('items') as $index => $item) {
                $rules["items.{$index}.sequence"][] = new GreaterThanPreviousSequence(
                    request()->input('items'), // Pass the entire 'items' array
                    $index,                    // Pass the current index
                    'sequence'                 // Pass the field name
                );
            }
        }
        return $rules;
    }
}
