<?php

namespace App\Http\Requests\Order\Shipment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderShipmentRequest extends FormRequest
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
                'sometimes',
                'exists:shipping_methods,id'
            ],
            'tracking_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:100'
            ],
            'carrier' => [
                'sometimes',
                'nullable',
                'string',
                'max:50'
            ],
            'weight' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0'
            ],
            'dimensions' => [
                'sometimes',
                'nullable',
                'string',
                'max:50'
            ],
            'notes' => [
                'sometimes',
                'nullable',
                'string'
            ],
        ];
    }
}
