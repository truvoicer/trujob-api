<?php

namespace App\Http\Requests\User;

use App\Models\Role;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserProfileRequest extends FormRequest
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
                Rule::unique('users')->ignore(request()->user()->user->id)->where(function (Builder $query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users')->ignore(request()->user()->id)->where(function (Builder $query) {
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
            'change_password' => [
                'sometimes',
                'boolean',
            ],
            'password' => [
                'required_if:change_password,true',
                'confirmed',
                Password::min(8)
            ],
        ];
    }
}
