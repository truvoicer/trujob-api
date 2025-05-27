<?php

namespace App\Http\Requests\Shipping\Method;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingMethodRequest extends FormRequest
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
                'sometimes',
                'string',
                'max:100'
            ],
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
        ];
    }
}
