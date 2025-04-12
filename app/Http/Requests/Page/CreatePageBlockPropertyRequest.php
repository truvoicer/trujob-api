<?php

namespace App\Http\Requests\Page;

use App\Enums\Block\BlockType;
use App\Enums\Pagination\PaginationScrollType;
use App\Enums\Pagination\PaginationType;
use App\Enums\Widget\Widget;
use App\Helpers\Tools\ValidationHelpers;
use App\Http\Requests\Listing\ListingFetchRequest;
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
            'type' => [
                'required',
                Rule::enum(BlockType::class),
            ],
            'properties' => [
                'sometimes',
                'array',
                'nullable',
            ],
            ...ValidationHelpers::buildRequestPropertyRules($type),
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
            ],
            'pagination_type' => [
                'sometimes',
                Rule::enum(PaginationType::class),
            ],
            'pagination_scroll_type' => [
                'sometimes',
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
