<?php

namespace App\Http\Requests\Price;

use Illuminate\Foundation\Http\FormRequest;

class StorePriceRequest extends FormRequest
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
            'amount' => ['required', 'numeric'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type_id' => ['required', 'integer', 'exists:price_types,id'],
            'valid_from' => ['required', 'date'],
            'valid_to' => ['nullable', 'date'],
            'is_default' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
