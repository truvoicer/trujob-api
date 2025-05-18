<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
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
            'name_plural' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:3'],
            'symbol' => ['required', 'string', 'max:3'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ];
    }
}
