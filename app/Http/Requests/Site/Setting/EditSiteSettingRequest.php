<?php

namespace App\Http\Requests\Site\Setting;

use Illuminate\Foundation\Http\FormRequest;

class EditSiteSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currency_id' => [
                'sometimes',
                'integer',
                'exists:currencies,id',
            ],
            'country_id' => [
                'sometimes',
                'integer',
                'exists:countries,id',
            ],
            'language_id' => [
                'sometimes',
                'integer',
                'exists:languages,id',
            ],
            'timezone' => [
                'sometimes',
                'string',
                'timezone',
            ],
            'frontend_url' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
