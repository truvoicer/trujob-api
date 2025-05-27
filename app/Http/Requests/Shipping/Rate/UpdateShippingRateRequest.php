<?php

namespace App\Http\Requests\Shipping\Rate;

use App\Enums\Order\Shipping\ShippingRateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShippingRateRequest extends FormRequest
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
            'shipping_method_id' => [
                'sometimes',
                'exists:shipping_methods,id'
            ],
            'shipping_zone_id' => [
                'sometimes',
                'exists:shipping_zones,id'
            ],
            'type' => [
                'sometimes',
                Rule::enum(ShippingRateType::class)
            ],
            'min_value' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'max_value' => [
                'nullable',
                'numeric',
                'min:0',
                'gt:min_value'
            ],
            'rate_amount' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'currency_id' => [
                'sometimes',
                'exists:currencies,id'
            ],
            'is_free_shipping_possible' => ['boolean'],
        ];
    }
}
