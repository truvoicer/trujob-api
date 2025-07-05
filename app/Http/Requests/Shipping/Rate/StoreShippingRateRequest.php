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
            'shipping_zone_id' => [
                'required',
                'exists:shipping_zones,id'
            ],
            'type' => [
                'required',
                Rule::enum(ShippingRateType::class)
            ],


            'currency_id' => [
                'required',
                'exists:currencies,id'
            ],
            'label' => [
                'required',
                'string',
                'max:50'
            ],
            'description' => [
                'required',
                'string'
            ],
            'is_active' => [
                'required',
                'boolean'
            ],
            'has_max_dimension' => ['sometimes', 'boolean'],
            'max_dimension' => ['required_if:has_max_dimension,true', 'numeric', 'min:0'],
            'max_dimension_unit' => [
                'required_if:has_max_dimension,true',
                'string',
                Rule::enum(ShippingUnit::class)
            ],

            'has_weight' => ['sometimes', 'boolean'],
            'has_height' => ['sometimes', 'boolean'],
            'has_width' => ['sometimes', 'boolean'],
            'has_depth' => ['sometimes', 'boolean'],

            'weight_unit' => [
                'required_if:has_weight,true',
                'string',
                Rule::enum(ShippingWeightUnit::class)
            ],
            'max_weight' => [
                'required_if:has_weight,true',
                'numeric',
                'min:0'
            ],
            'height_unit' => [
                'required_if:has_height,true',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'max_height' => [
                'required_if:has_height,true',
                'numeric',
                'min:0'
            ],
            'width_unit' => [
                'required_if:has_width,true',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'max_width' => [
                'required_if:has_width,true',
                'numeric',
                'min:0'
            ],
            'depth_unit' => [
                'required_if:has_depth,true',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'max_depth' => [
                'required_if:has_depth,true',
                'numeric',
                'min:0'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0'
            ],
            'dimensional_weight_divisor' => [
                'required',
                'numeric',
                'min:0'
            ],
        ];
    }
}
