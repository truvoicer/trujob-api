<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
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
            'name_plural' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:3'],
            'symbol' => ['sometimes', 'string', 'max:3'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

}
