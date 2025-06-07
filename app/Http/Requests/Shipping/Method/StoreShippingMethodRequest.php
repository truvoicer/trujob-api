<?php

namespace App\Http\Requests\Shipping\Method;

use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShippingMethodRequest extends FormRequest
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
            'carrier' => [
                'nullable',
                'string',
                'max:50'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'processing_time_days' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'order' => [
                'integer',
                'min:0'
            ],
            'is_active' => ['boolean'],
            'rates' => [
                'array',
                'nullable'
            ],
            'rates.*.shipping_zone_id' => [
                'required',
                'exists:shipping_zones,id'
            ],
            'rates.*.type' => [
                'required',
                'string',
                'in:flat_rate,free_shipping'
            ],
            'rates.*.weight_limit' => [
                'required',
                'boolean'
            ],
            'rates.*.height_limit' => [
                'required',
                'boolean'
            ],
            'rates.*.width_limit' => [
                'required',
                'boolean'
            ],
            'rates.*.length_limit' => [
                'required',
                'boolean'
            ],
            'rates.*.weight_unit' => [
                'required_if:rates.*.weight_limit,true',
                'string',
                Rule::enum(ShippingWeightUnit::class)
            ],
            'rates.*.height_unit' => [
                'required_if:rates.*.height_limit,true',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'rates.*.width_unit' => [
                'required_if:rates.*.width_limit,true',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'rates.*.length_unit' => [
                'required_if:rates.*.length_limit,true',
                'string',
                Rule::enum(ShippingUnit::class)
            ],
            'rates.*.min_weight' => [
                'required_if:rates.*.weight_limit,true',
                'numeric',
                'min:0'
            ],
            'rates.*.max_weight' => [
                'required_if:rates.*.weight_limit,true',
                'numeric',
                'min:0',
                'gt:rates.*.min_weight'
            ],
            'rates.*.min_height' => [
                'required_if:rates.*.height_limit,true',
                'numeric',
                'min:0'
            ],
            'rates.*.max_height' => [
                'required_if:rates.*.height_limit,true',
                'numeric',
                'min:0',
                'gt:rates.*.min_height'
            ],
            'rates.*.min_width' => [
                'required_if:rates.*.width_limit,true',
                'numeric',
                'min:0'
            ],
            'rates.*.max_width' => [
                'required_if:rates.*.width_limit,true',
                'numeric',
                'min:0',
                'gt:rates.*.min_width'
            ],
            'rates.*.min_length' => [
                'required_if:rates.*.length_limit,true',
                'numeric',
                'min:0'
            ],
            'rates.*.max_length' => [
                'required_if:rates.*.length_limit,true',
                'numeric',
                'min:0',
                'gt:rates.*.min_length'
            ],
            'rates.*.amount' => [
                'required',
                'numeric',
                'min:0'
            ],
            'rates.*.currency_id' => [
                'required',
                'exists:currencies,id'
            ],
            'rates.*.is_free_shipping_possible' => [
                'boolean'
            ],
            'rates.*.is_active' => [
                'boolean'
            ],
        ];
    }
}
