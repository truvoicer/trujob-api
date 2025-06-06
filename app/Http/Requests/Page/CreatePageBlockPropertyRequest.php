<?php

namespace App\Http\Requests\Page;

use App\Enums\Block\BlockType;
use App\Enums\Pagination\PaginationScrollType;
use App\Enums\Pagination\PaginationType;
use App\Enums\Widget\Widget;
use App\Helpers\Tools\ValidationHelpers;
use App\Http\Requests\Product\ProductFetchRequest;
use App\Models\Sidebar;
use App\Rules\IdOrNameExists;
use App\Rules\StringOrIntger;
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
    public function rules(?string $type = null): array
    {
        return [
            'properties' => [
                'sometimes',
                'array',
                'nullable',
            ],
            ...ValidationHelpers::buildRequestPropertyRules($type),
            'default' => [
                'sometimes',
                'boolean',
                'nullable',
            ],
            'nav_title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'subtitle' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'pagination' => [
                'sometimes',
                'boolean',
                'nullable',
            ],
            'background_color' => [
                'sometimes',
                'string',
                'max:255',
                'nullable',
            ],
            'background_image' => [
                'sometimes',
                'string',
                'max:255',
                'nullable',
            ],
            'pagination_type' => [
                'required_if_accepted:pagination',
                'nullable',
                Rule::enum(PaginationType::class),
            ],
            'pagination_scroll_type' => [
                'required_if_accepted:pagination',
                Rule::enum(PaginationScrollType::class),
                'nullable',
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
        ];
    }
}
