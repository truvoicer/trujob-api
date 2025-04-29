<?php

namespace App\Http\Requests\Widget;

use App\Models\Role;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateWidgetRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('widgets', 'name')->where(function ($query) {
                    return $query->where('site_id', request()->user()?->site?->id);
                }),
            ],
            'title' => [
                'sometimes',
                'string',
                'max:255',
                'nullable',
            ],
            'icon' => [
                'sometimes',
                'string',
                'max:255',
                'nullable',
            ],
            'description' => [
                'sometimes',
                'string',
                'max:255',
                'nullable',
            ],
            'properties' => [
                'sometimes',
                'array',
                'nullable',
            ],
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
