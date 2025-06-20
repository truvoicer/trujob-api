<?php

namespace App\Http\Requests\Shipping\Zone;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => [
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
            'is_active' => [
                'boolean'
            ],
            'all' => [
                'boolean',
                'nullable'
            ],
        ];
    }
}
