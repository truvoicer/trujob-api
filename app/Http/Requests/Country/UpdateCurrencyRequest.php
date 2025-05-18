<?php

namespace App\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'iso2' => ['sometimes', 'string', 'max:2'],
            'iso3' => ['sometimes', 'string', 'max:3'],
            'phone_code' => ['sometimes', 'string', 'max:5'],
        ];
    }
    
}
