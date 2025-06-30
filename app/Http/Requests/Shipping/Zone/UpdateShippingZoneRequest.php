<?php

namespace App\Http\Requests\Shipping\Zone;

use App\Enums\Order\Shipping\ShippingZoneAbleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShippingZoneRequest extends FormRequest
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
            'label' => [
                'sometimes',
                'string',
                'max:100'
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:500'
            ],
            'shipping_zoneables' => [
                'sometimes',
                'array'
            ],
            'shipping_zoneables.*.shipping_zoneable_id' => [
                'required',
                'integer',
            ],
            'shipping_zoneables.*.shipping_zoneable_type' => [
                Rule::enum(ShippingZoneAbleType::class)
            ],
            'all' => [
                'boolean',
                'nullable'
            ],
            'all' => [
                'boolean',
                'nullable'
            ],
        ];
    }
}
