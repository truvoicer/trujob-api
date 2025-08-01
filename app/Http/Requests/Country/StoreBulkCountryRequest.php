<?php

namespace App\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkCountryRequest extends FormRequest
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
            'countries' => ['required', 'array'],
            'countries.*.name' => ['required', 'string', 'max:255'],
            'countries.*.iso2' => ['required', 'string', 'max:2'],
            'countries.*.iso3' => ['required', 'string', 'max:3'],
            'countries.*.phone_code' => ['required', 'string', 'max:5'],
            'countries.*.is_active' => ['required', 'boolean'],
        ];
    }
}
