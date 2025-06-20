<?php

namespace App\Http\Requests\Shipping\Restriction;

use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Enums\Order\Shipping\ShippingRestrictionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShippingRestrictionRequest extends FormRequest
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
            'type' => [
                'required',
                Rule::enum(ShippingRestrictionType::class)
            ],
            'restriction_id' => [
                'required',
                'integer'
            ],
            'action' => [
                'required',
                Rule::enum(ShippingRestrictionAction::class)
            ],
        ];
    }
}
