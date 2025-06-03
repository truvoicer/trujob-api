<?php

namespace App\Http\Requests\Price;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePriceRequest extends FormRequest
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
            'amount' => ['sometimes', 'numeric'],
            'currency_id' => ['sometimes', 'integer', 'exists:currencies,id'],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'created_by_user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'type_id' => ['sometimes', 'integer', 'exists:price_types,id'],
            'valid_from' => ['sometimes', 'date'],
            'valid_to' => ['nullable', 'date'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
