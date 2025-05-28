<?php

namespace App\Http\Requests\Listing\Price;

use Illuminate\Foundation\Http\FormRequest;

class CreateListingPriceRequest extends FormRequest
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
            'price_type_id' => ['required', 'integer', 'exists:price_types,id'],
            'amount' => ['required', 'numeric'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'is_default' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'valid_from' => ['sometimes', 'date', 'nullable'],
            'valid_to' => ['sometimes', 'date', 'nullable', 'after:valid_from'],
        ];
    }
}
