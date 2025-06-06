<?php

namespace App\Http\Requests\Sidebar;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Requests\Widget\CreateWidgetRequest;
use App\Models\Role;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSidebarRequest extends FormRequest
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
        $widgetsRules = ValidationHelpers::nestedValidationRules(
            (new CreateWidgetRequest())->rules(), 
            'widgets.*'
        );
        $widgetsRules['widgets.*.name'] = [
            'required',
            'string',
            'max:255',
            Rule::exists('widgets', 'name')->where(function ($query) {
                return $query->where('site_id', request()->user()?->site?->id);
            }),
        ];
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sidebars', 'name')->where(function ($query) {
                    return $query->where('site_id', request()->user()?->site?->id);
                }),
            ],
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'icon' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'properties' => [
                'sometimes',
                'array',
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
            'widgets' => [
                'sometimes',
                'array',
            ],
            ...$widgetsRules
        ];
    }
}
