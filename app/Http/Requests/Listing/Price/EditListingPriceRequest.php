<?php

namespace App\Http\Requests\Listing\Price;

use Illuminate\Foundation\Http\FormRequest;

class EditListingPriceRequest extends FormRequest
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
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'type_id' => ['sometimes', 'integer', 'exists:listing_types,id'],
            'amount' => ['sometimes', 'numeric'],
            'currency_id' => ['sometimes', 'integer', 'exists:currencies,id'],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'valid_from' => ['sometimes', 'date'],
            'valid_to' => ['sometimes', 'date', 'after:valid_from'],
        ];
    }
}
