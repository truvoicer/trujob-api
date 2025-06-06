<?php

namespace App\Http\Requests\Page;

use App\Enums\PageSidebarWidget;
use App\Enums\ViewType;
use App\Helpers\Tools\ValidationHelpers;
use App\Models\Role;
use App\Models\Sidebar;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditPageRequest extends FormRequest
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
        $blocksRules = ValidationHelpers::nestedValidationRules((new EditPageBlockRequest())->rules(), 'blocks.*');
        unset($blocksRules['blocks.*.page_id']);
        return [
            'view' => [
                'sometimes',
                Rule::enum(ViewType::class)
            ],
            'permalink' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('pages', 'permalink')
                    ->where(function ($query) {
                        return $query->where('site_id', request()->user()?->id);
                    })
                    ->ignore($this->route('page'))
            ],
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('pages', 'name')
                ->where(function ($query) {
                    return $query->where('site_id', request()->user()?->id);
                })
                ->ignore($this->route('page'))
            ],
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'content' => [
                'sometimes',
                'string',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'is_home' => [
                'sometimes',
                'boolean',
            ],
            'is_featured' => [
                'sometimes',
                'boolean',
            ],
            'is_protected' => [
                'sometimes',
                'boolean',
            ],
            'has_sidebar' => [
                'sometimes',
                'boolean',
            ],
            'sidebars' => [
                'sometimes',
                'array',
            ],
            'sidebars.*' => [
                'required',
                new StringOrIntger,
                new IdOrNameExists(new Sidebar())
            ],
            'settings' => [
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
            'blocks' => [
                'sometimes',
                'array',
            ],
            ...$blocksRules
        ];
    }
}
