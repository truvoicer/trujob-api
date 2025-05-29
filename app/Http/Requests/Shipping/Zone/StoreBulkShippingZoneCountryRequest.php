<?php

namespace App\Http\Requests\Shipping\Zone;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkShippingZoneCountryRequest extends FormRequest
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
            'ids' => [
                'required',
                'array'
            ],
            'ids.*' => ['exists:countries,id'],
        ];
    }
}
