<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkCurrencyRequest extends FormRequest
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
            'currencies' => ['required', 'array'],
            'currencies.*.name' => ['required', 'string', 'max:255'],
            'currencies.*.name_plural' => ['required', 'string', 'max:255'],
            'currencies.*.code' => ['required', 'string', 'max:10'],
            'currencies.*.symbol' => ['required', 'string', 'max:10'],
            'currencies.*.is_active' => ['required', 'boolean'],
        ];
    }
}
