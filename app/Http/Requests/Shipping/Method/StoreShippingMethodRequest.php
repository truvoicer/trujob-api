<?php

namespace App\Http\Requests\Shipping\Method;

use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
use App\Helpers\Tools\ValidationHelpers;
use App\Http\Requests\Shipping\Rate\StoreShippingRateRequest;
use App\Http\Requests\Shipping\Restriction\StoreShippingRestrictionRequest;
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
        $restrictionRules = ValidationHelpers::nestedValidationRules(
            (new StoreShippingRestrictionRequest())->rules(),
            'restrictions.*'
        );
        $ratesRules = ValidationHelpers::nestedValidationRules(
            (new StoreShippingRateRequest())->rules(),
            'rates.*'
        );
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
            ...$ratesRules,
            'restrictions' => [
                'sometimes',
                'array'
            ],
            ...$restrictionRules,
        ];
    }
}
