<?php

namespace App\Http\Requests\Page;

use App\Enums\PageSidebarWidget;
use App\Enums\ViewType;
use App\Helpers\Tools\ValidationHelpers;
use App\Http\Requests\Menu\CreateMenuItemRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePageRequest extends FormRequest
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
        $blocksRules = ValidationHelpers::nestedValidation('blocks', (new CreatePageBlockRequest())->rules());
        unset($blocksRules['blocks.*.page_id']);
        return [
            'view' => [
                'required',
                Rule::enum(ViewType::class)
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')
            ],
            'title' => [
                'required',
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
            'sidebar_widgets' => [
                'sometimes',
                'array',
            ],
            'sidebar_widgets.*.type' => [
                'required_if:has_sidebar,true',
                Rule::enum(PageSidebarWidget::class)
            ],
            'sidebar_widgets.*.title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'sidebar_widgets.*.has_container' => [
                'required',
                'boolean',
            ],
            'sidebar_widgets.*.order' => [
                'sometimes',
                'integer',
            ],
            'sidebar_widgets.*.properties' => [
                'sometimes',
                'array',
            ],
            'settings' => [
                'sometimes',
                'array',
            ],
            'blocks' => [
                'sometimes',
                'array',
            ],
            ...$blocksRules
        ];
    }
}
