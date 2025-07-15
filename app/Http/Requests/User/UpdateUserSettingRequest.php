<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettingRequest extends FormRequest
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
            'app_theme' => ['sometimes', 'string', 'in:light,dark'],
            'push_notification' => ['sometimes', 'boolean'],
            'currency_id' => ['sometimes', 'exists:currencies,id'],
            'country_id' => ['sometimes', 'exists:countries,id'],
            'language_id' => ['sometimes', 'exists:languages,id'],
            'timezone' => ['sometimes', 'string', 'timezone'],
        ];
    }
}
