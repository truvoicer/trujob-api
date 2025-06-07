<?php

namespace App\Http\Requests\Shipping\Rate;

use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
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
            'weight_limit' => ['required', 'boolean'],
            'height_limit' => ['required', 'boolean'],
            'width_limit' => ['required', 'boolean'],
            'length_limit' => ['required', 'boolean'],
            'weight_unit' => [
                'required_if:weight_limit,true',
                Rule::enum(ShippingWeightUnit::class)
            ],
            'height_unit' => [
                'required_if:height_limit,true',
                Rule::enum(ShippingUnit::class)
            ],
            'width_unit' => [
                'required_if:width_limit,true',
                Rule::enum(ShippingUnit::class)
            ],
            'length_unit' => [
                'required_if:length_limit,true',
                Rule::enum(ShippingUnit::class)
            ],
            'min_weight' => [
                'required_if:weight_limit,true',
                'numeric',
                'min:0'
            ],
            'max_weight' => [
                'required_if:weight_limit,true',
                'numeric',
                'min:0',
                'gte:min_weight'
            ],
            'min_height' => [
                'required_if:height_limit,true',
                'numeric',
                'min:0'
            ],
            'max_height' => [
                'required_if:height_limit,true',
                'numeric',
                'min:0',
                'gte:min_height'
            ],
            'min_width' => [
                'required_if:width_limit,true',
                'numeric',
                'min:0'
            ],
            'max_width' => [
                'required_if:width_limit,true',
                'numeric',
                'min:0',
                'gte:min_width'
            ],
            'min_length' => [
                'required_if:length_limit,true',
                'numeric',
                'min:0'
            ],
            'max_length' => [
                'required_if:length_limit,true',
                'numeric',
                'min:0',
                'gte:min_length'
            ],
            'amount' => [
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
