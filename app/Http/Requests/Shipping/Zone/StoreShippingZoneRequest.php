<?php

namespace App\Http\Requests\Shipping\Zone;

use App\Enums\Order\Shipping\ShippingZoneAbleType;
use App\Models\ShippingZoneAble;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShippingZoneRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:100'
            ],
            'description' => [
                'nullable',
                'string',
                'max:500'
            ],
            'shipping_zoneables' => [
                'required',
                'array'
            ],
            'shipping_zoneables.*.id' => [
                'required',
                'integer',
            ],
            'shipping_zoneables.*.type' => [
                Rule::enum(ShippingZoneAbleType::class)
            ],
            'is_active' => ['boolean'],
            'all' => [
                'boolean',
                'nullable'
            ],
        ];
    }
}
