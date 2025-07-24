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
        $rules = [
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



            "label" => ['sometimes', 'string', 'max:255'],
            "description" => ['sometimes', 'string', 'max:1000'],
            'type' => [
                'sometimes',
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
                'sometimes',
                'numeric'
            ],
            "setup_fee_currency_id" => [
                'sometimes',
                'integer',
                'exists:currencies,id'
            ],
            'auto_bill_outstanding' => [
                'sometimes',
                'boolean'
            ],
            'setup_fee_failure_action' => [
                'sometimes',
                'string',
                Rule::enum(SubscriptionSetupFeeFailureAction::class)
            ],
            'payment_failure_threshold' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999'
            ],
            "items" => [
                'sometimes',
                'array'
            ],
            "items.*.id" => [
                'sometimes',
                'integer',
                'exists:price_subscription_items,id'
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
            'items.*.sequence' => ['required', 'integer', 'min:1'],
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
