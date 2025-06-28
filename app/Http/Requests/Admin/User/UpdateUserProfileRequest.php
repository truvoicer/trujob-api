<?php

namespace App\Http\Requests\Admin\User;

use App\Models\Role;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserProfileRequest extends FormRequest
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
            'first_name' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'last_name' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id)->where(function (Builder $query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id)->where(function (Builder $query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'dob' => [
                'sometimes',
                'date',
                'date_format:Y-m-d',
            ],
            'country_id' => [
                'sometimes',
                'exists:countries,id',
            ],
            'currency_id' => [
                'sometimes',
                'exists:currencies,id',
            ],
            'language_id' => [
                'sometimes',
                'exists:languages,id',
            ],
            'password' => ['confirmed', Password::min(8)],
            'roles' => [
                'sometimes',
                'array',
            ],
            'roles.*' => [
                'required',
                new StringOrIntger,
                new IdOrNameExists(new Role())
            ],
        ];
    }
}
