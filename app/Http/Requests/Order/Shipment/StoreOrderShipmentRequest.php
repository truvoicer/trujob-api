<?php

namespace App\Http\Requests\Order\Shipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderShipmentRequest extends FormRequest
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
            'tracking_number' => [
                'nullable',
                'string',
                'max:100'
            ],
            'carrier' => [
                'nullable',
                'string',
                'max:50'
            ],
            'weight' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'dimensions' => [
                'nullable',
                'string',
                'max:50'
            ],
            'notes' => [
                'nullable',
                'string'
            ],
        ];
    }
}
