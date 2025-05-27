<?php

namespace App\Http\Requests\Shipping\Rate;

use App\Enums\Order\Shipping\ShippingRateType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShippingRateRequest extends FormRequest
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
                'required',
                'exists:shipping_methods,id'
            ],
            'shipping_zone_id' => [
                'required',
                'exists:shipping_zones,id'
            ],
            'type' => [
                'required',
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
                'required',
                'numeric',
                'min:0'
            ],
            'currency_id' => [
                'required',
                'exists:currencies,id'
            ],
            'is_free_shipping_possible' => ['boolean'],
        ];
    }
}
