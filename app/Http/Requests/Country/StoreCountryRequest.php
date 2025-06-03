<?php

namespace App\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'iso2' => ['required', 'string', 'max:2'],
            'iso3' => ['required', 'string', 'max:3'],
            'phone_code' => ['required', 'string', 'max:5'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
