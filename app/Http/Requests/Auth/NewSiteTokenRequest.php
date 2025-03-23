<?php

namespace App\Http\Requests\Auth;

use App\Enums\Auth\ApiTokenExpiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewSiteTokenRequest extends FormRequest
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
            'expires_at' => Rule::enum(ApiTokenExpiry::class),
        ];
    }
}
