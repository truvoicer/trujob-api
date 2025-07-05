<?php

namespace App\Http\Requests\Shipping\Rate;

use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
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
            'shipping_zone_id' => [
                'sometimes',
                'exists:shipping_zones,id'
            ],
            'rate_type' => [
                'sometimes',
                Rule::enum(ShippingRateType::class)
            ],

            'currency_id' => [
                'sometimes',
                'exists:currencies,id'
            ],
            'label' => [
                'sometimes',
                'string',
                'max:50'
            ],
            'description' => [
                'sometimes',
                'string',
                'max:1000'
            ],
            'is_active' => [
                'boolean'
            ],

            'has_max_dimension' => ['sometimes', 'boolean'],
            'max_dimension' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'max_dimension_unit' => [
                'sometimes',
                'string',
                Rule::enum(ShippingUnit::class)
            ],

            'has_weight' => ['sometimes', 'boolean'],
            'has_height' => ['sometimes', 'boolean'],
            'has_width' => ['sometimes', 'boolean'],
            'has_depth' => ['sometimes', 'boolean'],
            'weight_unit' => [
                'sometimes',
                'string',
                Rule::enum(ShippingWeightUnit::class)
            ],
            'max_weight' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'height_unit' => [
                'sometimes',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'max_height' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'width_unit' => [
                'sometimes',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'max_width' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'depth_unit' => [
                'sometimes',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'max_depth' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'amount' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
            'dimensional_weight_divisor' => [
                'sometimes',
                'numeric',
                'min:0'
            ],
        ];
    }
}
