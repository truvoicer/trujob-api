<?php

namespace App\Http\Requests\Admin\User;

use App\Models\Role;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class CreateUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
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
