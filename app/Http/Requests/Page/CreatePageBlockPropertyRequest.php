<?php

namespace App\Http\Requests\Page;

use App\Enums\BlockType;
use App\Enums\ListingsBlockSidebarWidget;
use App\Enums\ViewType;
use App\Helpers\Tools\ValidationHelpers;
use App\Http\Requests\Menu\CreateMenuItemRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePageBlockPropertyRequest extends FormRequest
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
            'properties' => [
                'sometimes',
                'array',
            ],
            'properties.*.title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'properties.*.subtitle' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'properties.*.sidebar_widgets' => [
                'sometimes',
                'array',
            ],
            'properties.*.sidebar_widgets.*.name' => [
                'required_if:type,' . BlockType::LISTINGS_GRID->value,
                Rule::enum(ListingsBlockSidebarWidget::class)
            ],
            'properties.*.sidebar_widgets.*.title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'properties.*.sidebar_widgets.*.has_container' => [
                'required',
                'boolean',
            ],
        ];
    }
}
